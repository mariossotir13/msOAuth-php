<?php

namespace Ms\OauthBundle\Component\Authorization;

use Doctrine\Common\Persistence\ObjectRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ExecutionContextInterface;
use Ms\OauthBundle\Entity\AuthorizationCodeProfile;

/**
 * Description of AccessTokenRequest
 *
 * @author Marios
 */
class AccessTokenRequest {
    
    /**
     * @var string
     */
    const SERVER_URI = 'http://msoauthphp.local/app_dev.php/authorization/access_token';
    
    /**
     * @var string
     */
    const QUERY_PARAM = 'accTkn_rq';
    
    /**#@+
     * 
     * @var string
     */
    private static $CLIENT_ID = 'client_id';
    private static $CODE = 'code';
    private static $GRANT_TYPE = 'grant_type';
    private static $REDIRECTION_URI = 'redirect_uri';
    /**#@-*/

    /**
     *
     * @var string
     */
    private $clientId;

    /**
     *
     * @var ObjectRepository
     */
    private $authorizationCodeRepo;

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
    private $oauthServerUri;

    /**
     *
     * @var string
     */
    private $redirectionUri;

    /**
     * 
     * @param Request $request
     * @return AccessTokenRequest
     */
    public static function fromRequest(Request $request) {
        $accessTokenRequest = new AccessTokenRequest(self::SERVER_URI);
        $accessTokenRequest->setClientId($request->query->get(static::$CLIENT_ID));
        $accessTokenRequest->setRedirectionUri($request->query->get(static::$REDIRECTION_URI));
        $accessTokenRequest->setGrantType($request->query->get(static::$GRANT_TYPE));
        $accessTokenRequest->setCode($request->query->get(static::$CODE));

        return $accessTokenRequest;
    }
    
    /**
     * Δημιουργεί ένα νέο στιγμιότυπο της κλάσης AccessTokenRequest αντλώντας
     * δεδομένα από ένα URI.
     * 
     * @param string $uri
     * @return AccessTokenRequest
     * @throws \InvalidArgumentException εάν το όρισμα `$uri` είναι `null`.
     */
    public static function fromUri($uri) {
        if ($uri === null) {
            throw new \InvalidArgumentException('No uri was provided.');
        }
        
        $request = new AccessTokenRequest(self::SERVER_URI);
        $request->setClientId(static::extractParameterFromUri($uri, static::$CLIENT_ID));
        $request->setRedirectionUri(static::extractParameterFromUri($uri, static::$REDIRECTION_URI));
        $request->setCode(static::extractParameterFromUri($uri, static::$CODE));
        $request->setGrantType(static::extractParameterFromUri($uri, static::$GRANT_TYPE));
        
        return $request;
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
     * @param string $clientId
     * @return void
     */
    public function setClientId($clientId) {
        $this->clientId = $clientId;
    }

    /**
     * 
     * @param ObjectRepository $authorizationCodeRepo
     * @return void
     * @throws \InvalidArgumentException εάν το όρισμα `$authorizationCodeRepo`
     * είναι `null`.
     */
    public function setAuthorizationCodeRepository(ObjectRepository $authorizationCodeRepo) {
        if ($authorizationCodeRepo === null) {
            throw new \InvalidArgumentException('No client repository was specified.');
        }
        $this->authorizationCodeRepo = $authorizationCodeRepo;
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
    
    /**
     * 
     * @param ExecutionContextInterface $context
     * @return void
     */
    public function validateCode(ExecutionContextInterface $context) {
        if ($this->authorizationCodeRepo === null) {
            throw new \BadMethodCallException('No repository for AuthorizationCodeProfile has been specified.');
        }
        
        $codeProfile = $this->authorizationCodeRepo->findOneByAuthorizationCode($this->code);
        if ($codeProfile === null) {
            return $this->addCodeViolation($context, 'Invalid authorization code.');
        }
        
        if ($this->hasCodeExpired($codeProfile)) {
            return $this->addCodeViolation($context, 'The authorization code has expired.');
        }
    }
    
    /**
     * 
     * @param ExecutionContextInterface $context
     * @param string $errorMessage
     * @return void
     */
    protected function addCodeViolation(ExecutionContextInterface $context, $errorMessage) {
        $context->addViolationAt('code', $errorMessage);
    }
    
    /**
     * 
     * @param AuthorizationCodeProfile $profile
     * @return bool
     */
    private function hasCodeExpired(AuthorizationCodeProfile $profile) {
        $expirationDate = $profile->getExpirationDate();
        $now = new \DateTime('now', new \DateTimeZone('UTC'));
        
        return $expirationDate <= $now;
    }
}
