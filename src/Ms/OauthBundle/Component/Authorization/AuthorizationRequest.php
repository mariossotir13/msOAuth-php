<?php

namespace Ms\OauthBundle\Component\Authorization;

use Symfony\Component\HttpFoundation\Request;

/**
 * Αποθηκεύει τις πληροφορίες για μία αίτηση για κωδικό εξουσιοδότησης.
 *
 * @author Marios
 */
class AuthorizationRequest {
    
    /**#@+
     *
     * @var string
     */
    private static $CLIENT_ID = 'client_id';
    private static $REDIRECTION_URI = 'redirect_uri';
    private static $RESPONSE_TYPE = 'response_type';
    private static $SCOPE = 'scope';
    private static $SERVER_URI = 'http://msoauthphp.local/web/app_dev.php/authorization';
    private static $STATE = 'state';
    /**#@-*/
    
    /**
     *
     * @var string
     */
    private $clientId;
    
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
    private $redirectionUri;
    
    /**
     *
     * @var AuthorizationResponseType
     */
    private $responseType;
    
    /**
     *
     * @var AuthorizationCodeScope[]
     */
    private $scopes;
    
    /**
     *
     * @var string
     */
    private $state;
    
    /**
     * Δημιουργεί ένα νέο στιγμιότυπο της κλάσης AuthorizationRequest αντλώντας
     * δεδομένα από ένα Request αντικείμενο.
     * 
     * @param Request $request
     * @return AuthorizationRequest
     */
    public static function fromRequest(Request $request) {
        $authorizationRequest = new AuthorizationRequest($request->request->get(static::$SERVER_URI));
        $authorizationRequest->setClientId($request->request->get(static::$CLIENT_ID));
        $authorizationRequest->setResponseType($request->request->get(static::$RESPONSE_TYPE));
        $authorizationRequest->setState($request->request->get(static::$STATE));
        
        $scopes = $this->extractScopes($request);
        foreach ($scopes as $scope) {
            $authorizationRequest->addScope($scope);
        }
        
        return $authorizationRequest;
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
     * @throws \InvalidArgumentException εάν η παράμετρος `$scope` είναι *null*
     * ή δεν είναι έγκυρη η τιμή της.
     */
    function addScope($scope) {
        if ($scope === null) {
            throw new \InvalidArgumentException('No scope was specified.');
        }
        if (!in_array($scope, AuthorizationCodeScope::getScopes())) {
            throw new \InvalidArgumentException('Invalid scope: ' . $scope);
        }
        if (!in_array($scope, $this->scopes)) {
            $this->scopes[] = $scope;
        }
    }
    
    /**
     * 
     * @return AuthorizationCodeScope[]
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
     */
    public function setRedirectionUri($redirectionUri) {
        if ($redirectionUri === null) {
            throw new \InvalidArgumentException('No redirection URI was specified.');
        }
        $this->redirectionUri = $redirectionUri;
    }

    /**
     * 
     * @param AuthorizationResponseType $responseType
     */
    public function setResponseType(AuthorizationResponseType $responseType) {
        if ($responseType === null) {
            throw new \InvalidArgumentException('No response type was specified.');
        }
        $this->responseType = $responseType;
    }

    /**
     * 
     * @param string $state
     */
    public function setState($state) {
        if ($state === null) {
            throw new \InvalidArgumentException('No state was specified.');
        }
        $this->state = $state;
    }
    
    /**
     * @return string
     */
    public function toUri() {
        $uri = '';
        $uri .= $this->clientId ? '&' . static::$CLIENT_ID . '=' . $this->clientId : '';
        $uri .= $this->redirectionUri ? '&' . static::$REDIRECTION_URI . '=' . $this->redirectionUri : '';
        $uri .= $this->responseType ? '&' . static::$RESPONSE_TYPE . '=' . $this->responseType : '';
        $uri .= $this->state ? '&' . static::$STATE . '=' . $this->state : '';
        $uri .= $this->scope ? '&' . static::$SCOPE . '=' . $this->formatScopes() : '';
        
        return urlencode($uri);
    }
    
    /**
     * 
     * @param Request $request
     * @return array
     */
    private function extractScopes(Request $request) {
        $scopes = $request->request->get(static::$SCOPE);
        
        return split(' ', $scopes);
    }
    
    /**
     * @return string
     */
    private function formatScopes() {
        return implode(' ', $this->scopes);
    }
}