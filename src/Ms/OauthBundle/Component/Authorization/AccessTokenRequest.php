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
    
    /**#@+
     * 
     * @var string
     */
    private static $CLIENT_ID = 'client_id';
    private static $REDIRECTION_URI = 'redirect_uri';
    private static $GRANT_TYPE = 'grant_type';
    private static $CODE = 'code';
    /**#@-*/

    /**
     *
     * @var string
     */
    private $clientId;

    /**
     *
     * @var string
     */
    private $code;

    /**
     *
     * @var string
     */
    private $grantType;

    /**
     *
     * @var string
     */
    private $redirectionUri;

    /**
     *
     * @var ObjectRepository
     */
    private $clientRepository;

    /**
     * 
     * @param Request $request
     * @return AccessTokenRequest
     */
    public static function fromRequest(Request $request) {
        $accessTokenRequest = new AccessTokenRequest();
        $accessTokenRequest->setClientId($request->query->get(static::$CLIENT_ID));
        $accessTokenRequest->setRedirectionUri($request->query->get(static::$REDIRECTION_URI));
        $accessTokenRequest->setGrantType($request->query->get(static::$GRANT_TYPE));
        $accessTokenRequest->setCode($request->query->get(static::$CODE));

        return $accessTokenRequest;
    }

    /**
     * 
     */
    function __construct() {
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
    public function getCode() {
        return $this->code;
    }

    /**
     * 
     * @return string
     */
    public function getGrantType() {
        return $this->grantType;
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
     * @return boolean
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
     * @param string $code
     * @return void
     */
    public function setCode($code) {
        $this->code = $code;
    }

    /**
     * 
     * @param string $grantType
     * @return void
     */
    public function setGrantType($grantType) {
        $this->grantType = $grantType;
    }

    /**
     * 
     * @param string $redirectionUri
     * @return void
     */
    public function setRedirectionUri($redirectionUri) {
        $this->redirectionUri = $redirectionUri;
    }
}
