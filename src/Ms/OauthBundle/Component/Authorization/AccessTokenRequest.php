<?php

namespace Ms\OauthBundle\Component\Authorization;

use Doctrine\Common\Persistence\ObjectRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ExecutionContextInterface;
use Ms\OauthBundle\Entity\AuthorizationCodeProfile;
use Doctrine\Bundle\DoctrineBundle\Registry;

/**
 * Description of AccessTokenRequest
 *
 * @author Marios
 */
class AccessTokenRequest {
    
    /**
     * @var string
     */
    const SERVER_URI = 'http://msoauthphp.local/app_dev.php/oauth2/c/authorization/access_token';
    
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
     * @var ObjectRepository
     */
    private $authorizationCodeRepo;

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
     * @var Registry
     */
    private $doctrineRegistry;

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
        $accessTokenRequest->setClientId($request->request->get(static::$CLIENT_ID));
        $accessTokenRequest->setRedirectionUri($request->request->get(static::$REDIRECTION_URI));
        $accessTokenRequest->setGrantType($request->request->get(static::$GRANT_TYPE));
        $accessTokenRequest->setCode($request->request->get(static::$CODE));

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
     * @throws \InvalidArgumentException εάν κάποιο όρισμα είναι `null`.
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
     * @param Registry $registry
     * @return void
     * @throws \InvalidArgumentException
     */
    public function setDoctrineRegistry(Registry $registry) {
        if ($registry === null) {
            throw new \InvalidArgumentException('No registry was specified.');
        }
        $this->doctrineRegistry = $registry;
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
     * @return string[]
     */
    public function toArray() {
        return array(
            static::$CLIENT_ID => $this->getClientId(),
            static::$CODE => $this->getCode(),
            static::$GRANT_TYPE => $this->getGrantType(),
            static::$REDIRECTION_URI => $this->getRedirectionUri()
        );
    }
    
    /**
     * @var string
     */
    public function toUri($serverUriIncluded = false) {
        $uri = static::$CLIENT_ID . '=' . urlencode($this->getClientId()) . '&'
            . static::$CODE . '=' . urlencode($this->getCode()) . '&'
            . static::$GRANT_TYPE . '=' . urlencode($this->getGrantType()) . '&'
            . static::$REDIRECTION_URI . '=' . urlencode($this->getRedirectionUri());
        if ($serverUriIncluded) {
            $uri = self::SERVER_URI . '?' . $uri;
        }
        
        return $uri;
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
        if ($this->doctrineRegistry === null) {
            throw new \BadMethodCallException('No Doctrine registry has been specified.');
        }
        
        $codeProfile = $this->authorizationCodeRepo->findOneByAuthorizationCode($this->code);
        if ($codeProfile === null) {
            return $this->addCodeViolation($context, 'Invalid authorization code.');
        }
        if ($this->isCodeExpired($codeProfile)) {
            return $this->addCodeViolation($context, 'The authorization code has expired.');
        }
        if (!$this->isCodeAuthored($codeProfile, $this->clientId, $this->redirectionUri)) {
            return $this->addCodeViolation(
                $context, 
                'No authorization code has been issued for this combination of client ID and redirection URI.'
            );
        }
        if ($this->isCodeExchanged($codeProfile)) {
            $this->revokeCode($codeProfile);
            
            return $this->addCodeViolation($context, 'Authorization code already exchanged.');
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
     * @param string $clientId
     * @param string $redirectionUri
     * @return boolean
     */
    private function isCodeAuthored(AuthorizationCodeProfile $profile, $clientId, $redirectionUri) {
        if (empty($clientId)
                || (empty($redirectionUri))) {
            return false;
        }
        
        if ($redirectionUri !== $profile->getRedirectionUri()) {
            return false;
        }
        
        $client = $profile->getClient();
        if ($client === null
                || $clientId !== $client->getId()) {
            return false;
        }
        
        return true;
    }
    
    /**
     * 
     * @param AuthorizationCodeProfile $profile
     * @return boolean
     */
    private function isCodeExchanged(AuthorizationCodeProfile $profile) {
        $tokenProfile = $profile->getAccessTokenProfile();
        
        return $tokenProfile !== null;
    }
    
    /**
     * 
     * @param AuthorizationCodeProfile $profile
     * @return boolean
     */
    private function isCodeExpired(AuthorizationCodeProfile $profile) {
        $expirationDate = $profile->getExpirationDate();
        $now = new \DateTime('now', new \DateTimeZone('UTC'));
        
        return $expirationDate <= $now;
    }
    
    /**
     * @param AuthorizationCodeProfile $profile
     * @return void
     */
    private function revokeCode(AuthorizationCodeProfile $profile) {
        $tokenProfile = $profile->getAccessTokenProfile();
        
        $em = $this->doctrineRegistry->getManager();
        $em->remove($tokenProfile);
        $em->remove($profile);
        $em->flush();
    }
}
