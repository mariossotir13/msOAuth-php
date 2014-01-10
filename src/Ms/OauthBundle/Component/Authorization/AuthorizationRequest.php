<?php

namespace Ms\OauthBundle\Component\Authorization;

use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectRepository;

/**
 * Αποθηκεύει τις πληροφορίες για μία αίτηση για κωδικό εξουσιοδότησης.
 * 
 * ### Προϋποθέσεις ###
 * 
 * Αυτή η κλάση υποθέτει ότι τα δεδομένα τα οποία της παρέχονται επικυρώνται μέσω
 * της Υπηρεσίας Επικύρωσης.
 *
 * @author Marios
 */
class AuthorizationRequest {
    
    /**
     * @var string
     */
    const QUERY_PARAM = 'authz_rq';
    
    /**#@+
     *
     * @var string
     */
    private static $CLIENT_ID = 'client_id';
    private static $REDIRECTION_URI = 'redirect_uri';
    private static $RESPONSE_TYPE = 'response_type';
    private static $SCOPE = 'scope';
    private static $SERVER_URI = 'http://msoauthphp.local/app_dev.php/authorization';
    private static $STATE = 'state';
    /**#@-*/
    
    /**
     *
     * @var string
     */
    private $clientId = '';
    
    /**
     *
     * @var ObjectRepository
     */
    private $clientRepository;
    
    /**
     * Η διεύθυνση URI του Εξυπηρετητή Εξουσιοδοτήσεων.
     *
     * @var string
     */
    private $oauthServerUri;
    
    /**
     *
     * @var string
     */
    private $redirectionUri = '';
    
    /**
     *
     * @var string
     */
    private $responseType = '';
    
    /**
     *
     * @var string[]
     */
    private $scopes = array();
    
    /**
     *
     * @var string
     */
    private $state = '';
    
    /**
     * Δημιουργεί ένα νέο στιγμιότυπο της κλάσης AuthorizationRequest αντλώντας
     * δεδομένα από ένα Request αντικείμενο.
     * 
     * @param Request $request
     * @return AuthorizationRequest
     */
    public static function fromRequest(Request $request) {
        $authorizationRequest = new AuthorizationRequest(static::$SERVER_URI);
        $authorizationRequest->setClientId($request->query->get(static::$CLIENT_ID));
        $authorizationRequest->setRedirectionUri($request->query->get(static::$REDIRECTION_URI));
        $authorizationRequest->setResponseType($request->query->get(static::$RESPONSE_TYPE));
        $authorizationRequest->setScopes($request->query->get(static::$SCOPE));
        $authorizationRequest->setState($request->query->get(static::$STATE));
        
        return $authorizationRequest;
    }
    
    /**
     * Δημιουργεί ένα νέο στιγμιότυπο της κλάσης AuthorizationRequest αντλώντας
     * δεδομένα από ένα URI.
     * 
     * @param string $uri
     * @return AuthorizationRequest
     * @throws \InvalidArgumentException εάν το όρισμα `$uri` είναι `null`.
     */
    public static function fromUri($uri) {
        if ($uri === null) {
            throw new \InvalidArgumentException('No uri was provided.');
        }
        
        $request = new AuthorizationRequest(static::$SERVER_URI);
        $request->setClientId(static::extractParameterFromUri($uri, static::$CLIENT_ID));
        $request->setRedirectionUri(static::extractParameterFromUri($uri, static::$REDIRECTION_URI));
        $request->setResponseType(static::extractParameterFromUri($uri, static::$RESPONSE_TYPE));
        $request->setScopes(static::extractParameterFromUri($uri, static::$SCOPE));
        $request->setState(static::extractParameterFromUri($uri, static::$STATE));
        
        return $request;
    }
    
    /**
     * @param string $oauthServerUri
     */
    function __construct($oauthServerUri) {
        if ($oauthServerUri === null) {
            throw new \InvalidArgumentException('No authorization server URI was specified.');
        }
        $this->oauthServerUri = $oauthServerUri;
    }

    /**
     * 
     * @param string $scope
     * @return void
     * @throws \InvalidArgumentException εάν η παράμετρος `$scope` είναι *null*
     * ή δεν είναι έγκυρη η τιμή της.
     */
    function addScope($scope) {
        if (!in_array($scope, $this->scopes)) {
            $this->scopes[] = $scope;
        }
    }
    
    /**
     * 
     * @return string[]
     */
    public function getScopes() {
        return $this->scopes;
    }

    /**
     * 
     * @return string
     */
    public function getClientId() {
        return $this->clientId;
    }

    /**
     * 
     * @return string
     */
    public function getOauthServerUri() {
        return $this->oauthServerUri;
    }
    
    /**
     * 
     * @return string
     */
    public function getRedirectionUri() {
        return $this->redirectionUri;
    }

    /**
     * 
     * @return string
     */
    public function getResponseType() {
        return $this->responseType;
    }

    /**
     * 
     * @return string
     */
    public function getState() {
        return $this->state;
    }
    
    /**
     * Ελέγχει εάν το *Αναγνωριστικό Πελάτη* του παρόντος αντικειμένου αντιστοιχεί
     * σε εγγεγραμμένο *Πελάτη*.
     * 
     * Αυτή η μέθοδος χρησιμοποιείται από την *Υπηρεσία Επικύρωσης Δεδομένων* για
     * την επικύρωση του *Αναγνωριστικού Πελάτη*.
     * 
     * @return bool `true` εάν το *Αναγνωριστικό Πελάτη* αντιστοιχεί σε εγγεγραμμένο
     * *Πελάτη*, αλλιώς `false`.
     */
    public function isClientIdValid() {
        return $this->clientId !== null
            && $this->clientRepository->find($this->clientId) !== null;
    }

    /**
     * 
     * @param string $clientId
     * @return void
     */
    public function setClientId($clientId) {
        $this->clientId = $clientId;
    }
    
    /**
     * 
     * @param ObjectRepository $clientRepository
     * @return void
     * @throws \InvalidArgumentException εάν το όρισμα `$clientRepository` είναι
     * `null`.
     */
    public function setClientRepository(ObjectRepository $clientRepository) {
        if ($clientRepository === null) {
            throw new \InvalidArgumentException('No client repository was specified.');
        }
        $this->clientRepository = $clientRepository;
    }
    
    /**
     * 
     * @param string $redirectionUri
     * @return void
     */
    public function setRedirectionUri($redirectionUri) {
        $this->redirectionUri = $redirectionUri;
    }

    /**
     * 
     * @param string $responseType
     * @return void
     */
    public function setResponseType($responseType) {
        $this->responseType = $responseType;
    }
    
    /**
     * Θέτει όλα τα Πεδία Τεκμηρίου Πρόσβασης από μία συμβολοσειρά διαχωριζόμενη
     * με κενά (U+0020).
     * 
     * @param string $scopesString
     * @return void
     */
    public function setScopes($scopesString) {
        $scopes = $this->extractScopes($scopesString);
        foreach ($scopes as $scope) {
            $this->addScope($scope);
        }
    }

    /**
     * 
     * @param string $state
     * @return void
     */
    public function setState($state) {
        $this->state = $state;
    }
    
    /**
     * @return string[]
     */
    public function toArray() {
        $result = array();
        $result[static::$CLIENT_ID] = $this->getClientId();
        $result[static::$REDIRECTION_URI] = $this->getRedirectionUri();
        $result[static::$RESPONSE_TYPE] = $this->getResponseType();
        $result[static::$SCOPE] = $this->formatScopes();
        $result[static::$STATE] = $this->getState();
        
        return $result;
    }
    
    /**
     * 
     * @return string
     * @throws \BadMethodCallException εάν αυτή η μέθοδος κληθεί προτού τεθεί
     * τουλάχιστον μία από τις παραμέτρους μίας Αίτησης Εξουσιοδότησης.
     */
    public function toQueryStringParameterValue() {
        $queryStringParameters = $this->toArray();
        if (empty($queryStringParameters)) {
            throw new \BadMethodCallException('No parameters have been set to this request.');
        }
        
        $param = '';
        foreach ($queryStringParameters as $key => $value) {
            $param .= $key . '=' . $value . '&';
        }
        
        return substr($param, 0, -1);
    }
    
    /**
     * @return string
     */
    public function toUri() {        
        $queryStringParameters = $this->toArray();
        $queryString = '';
        foreach ($queryStringParameters as $key => $value) {
            $queryString .= $key . '=' . urlencode($value);
        }
        
        $uri = $this->oauthServerUri;
        $uri .= ($queryString !== '') ? '?' . $queryString : '';
        
        return $uri;
    }
    
    /**
     * 
     * @param string $uri
     * @param string $paramName
     * @return string
     * @throws \InvalidArgumentException εάν το `$uri` δεν περιέχει την παράμετρο
     * `$paramName`.
     */
    protected static function extractParameterFromUri($uri, $paramName) {
        $pattern = '#' . $paramName . '=([^&]+)#';
        $matches = array();
        $matched = preg_match($pattern, $uri, $matches);
        if ($matched !== 1) {
            throw new \InvalidArgumentException('Missing parameter "' . $paramName . '" from URI: '. $uri);
        }
        
        return $matches[1];
    }
    
    /**
     * 
     * @param string $scopesString
     * @return array
     */
    protected function extractScopes($scopesString) {
        return preg_split('/\s/', $scopesString);
    }
    
    /**
     * 
     * @return string
     */
    protected function formatScopes() {
        $scopes = $this->scopes ?: array();
        
        return implode(' ', $scopes);
    }
}