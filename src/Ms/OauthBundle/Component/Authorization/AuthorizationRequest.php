<?php

namespace Ms\OauthBundle\Component\Authorization;

use Symfony\Component\HttpFoundation\Request;
use Ms\OauthBundle\Entity\AuthorizationCodeScope;

/**
 * Αποθηκεύει τις πληροφορίες για μία αίτηση για κωδικό εξουσιοδότησης.
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
     * Η διεύθυνση URI του εξυπηρετητή εξουσιοδοτήσεων.
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
        if ($scope === null) {
            throw new \InvalidArgumentException('No scope was specified.');
        }
        if (!in_array($scope, AuthorizationCodeScope::getValues())) {
            throw new \InvalidArgumentException('Invalid scope: ' . $scope);
        }
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
     * 
     * @param string $clientId
     * @return void
     */
    public function setClientId($clientId) {
        if ($clientId === null) {
            throw new \InvalidArgumentException('No client id was specified.');
        }
        $this->clientId = $clientId;
    }
    
    /**
     * 
     * @param string $redirectionUri
     * @return void
     */
    public function setRedirectionUri($redirectionUri) {
        if ($redirectionUri === null) {
            throw new \InvalidArgumentException('No redirection URI was specified.');
        }
        $this->redirectionUri = $redirectionUri;
    }

    /**
     * 
     * @param string $responseType
     * @return void
     */
    public function setResponseType($responseType) {
        if ($responseType === null) {
            throw new \InvalidArgumentException('No response type was specified.');
        }
        if (!in_array($responseType, AuthorizationResponseType::getValues())) {
            throw new \InvalidArgumentException('Invalid response type: ' . $responseType);
        }
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
        if ($state === null) {
            throw new \InvalidArgumentException('No state was specified.');
        }
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
     */
    public function toQueryStringParameterValue() {
        $param = '';
        $param .= $this->clientId ? static::$CLIENT_ID . '=' . $this->clientId : '';
        $param .= $this->redirectionUri ? '&' . static::$REDIRECTION_URI . '=' . $this->redirectionUri : '';
        $param .= $this->responseType ? '&' . static::$RESPONSE_TYPE . '=' . $this->responseType : '';
        $param .= $this->state ? '&' . static::$STATE . '=' . $this->state : '';
        $param .= $this->scopes ? '&' . static::$SCOPE . '=' . $this->formatScopes() : '';
        
        return $param;
    }
    
    /**
     * @return string
     */
//    public function toUri() {
//        $uri = '';
//        $uri .= $this->clientId ? '&' . static::$CLIENT_ID . '=' . $this->clientId : '';
//        $uri .= $this->redirectionUri ? '&' . static::$REDIRECTION_URI . '=' . $this->redirectionUri : '';
//        $uri .= $this->responseType ? '&' . static::$RESPONSE_TYPE . '=' . $this->responseType : '';
//        $uri .= $this->state ? '&' . static::$STATE . '=' . $this->state : '';
//        $uri .= $this->scopes ? '&' . static::$SCOPE . '=' . $this->formatScopes() : '';
//        
//        return $uri;
//    }
    
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