<?php

namespace Ms\OauthBundle\Component\Authorization;

use Doctrine\Common\Persistence\ObjectRepository;
use Symfony\Component\HttpFoundation\Request;

/**
 * Description of AccessTokenRequest
 *
 * @author Marios
 */
class AccessTokenRequest {
    
    private static $CLIENT_ID = 'client_id';
    private static $REDIRECTION_URI = 'redirect_uri';
    private static $GRANT_TYPE = 'grant_type';
    private static $CODE = 'code';
    
    /**
     *
     * @var string
     */
    private $grantType;
    
    /**
     *
     * @var string
     */
    private $code;
    
    /**
     *
     * @var string
     */
    private $redirectionUri;
    
    /**
     *
     * @var string
     */
    private $clientId;
    
    /**
     *
     * @var ObjectRepository
     */
    private $clientRepository;
    
     public static function fromRequest(Request $request, ObjectRepository $clientRepository) {
        $accessTokenRequest = new AccessTokenRequest($clientRepository);
        $accessTokenRequest->setClientId($request->query->get(static::$CLIENT_ID));
        $accessTokenRequest->setRedirectionUri($request->query->get(static::$REDIRECTION_URI));
        $accessTokenRequest->setGrantType($request->query->get(static::$GRANT_TYPE));
        $accessTokenRequest->setCode($request->query->get(static::$CODE));
        
        return $accessTokenRequest;
    }
    
    function __construct(ObjectRepository $clientRepository) {
        if($clientRepository === null) {
            throw new \InvalidArgumentException('No client repository was provided.');
        }
        $this->clientRepository = $clientRepository;
    }

    
    public function isClientIdValid() {
        return $this->clientRepository->find($this->clientId) !== null;
    }
    
    public function getGrantType() {
        return $this->grantType;
    }

    public function setGrantType($grantType) {
        $this->grantType = $grantType;
    }

    public function getCode() {
        return $this->code;
    }

    public function setCode($code) {
        $this->code = $code;
    }

    public function getRedirectionUri() {
        return $this->redirectionUri;
    }

    public function setRedirectionUri($redirectionUri) {
        $this->redirectionUri = $redirectionUri;
    }

    public function getClientId() {
        return $this->clientId;
    }

    public function setClientId($clientId) {
        $this->clientId = $clientId;
    }


}