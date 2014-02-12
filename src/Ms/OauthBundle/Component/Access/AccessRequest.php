<?php

namespace Ms\OauthBundle\Component\Access;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ExecutionContextInterface;
use Buzz\Browser;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\DependencyInjection\Container;

/**
 * Description of AccessRequest
 *
 * @author Marios
 */
class AccessRequest {
    
    /**
     * @var string
     */
    const PARAM_NAME = 'name';
    
    /**#@+
     *
     * @var string
     */
    protected static $OAUTH_TOKEN_INFO_URL = 
        'http://msoauthphp.local/app_dev.php/oauth2/c/tokenInfo/';
    protected static $OAUTH_TOKEN_VALIDATION_URL = 
        'http://msoauthphp.local/app_dev.php/oauth2/c/accessToken/validation/';
    /**#@-*/
    
    /**
     *
     * @var string
     */
    private $accessToken;

    /**
     *
     * @var Browser
     */
    private $browser;
    
    /**
     *
     * @var Container
     */
    private $container;

    /**
     *
     * @var string
     */
    private $resourceName;
    
    /**
     * 
     * @param Request $request
     * @param Browser $browser
     * @param Container $container
     * @return AccessRequest
     * @throws \InvalidArgumentException
     */
    public static function fromRequest(Request $request, Browser $browser, Container $container) {
        if ($request === null) {
            throw new \InvalidArgumentException('No request was provided.');
        }
        
        $accessRequest = new AccessRequest($request->get(self::PARAM_NAME), $browser, $container);
        $accessRequest->setAccessToken(static::extractAccessToken($request));
        
        return $accessRequest;
    }

    /**
     * 
     * @param string $resourceName
     * @param Browser $browser
     */
    function __construct($resourceName, Browser $browser, Container $container) {
        if ($browser === null) {
            throw new \InvalidArgumentException('No browser was provided.');
        }
        $this->resourceName = $resourceName;
        $this->browser = $browser;
        $this->container = $container;
    }

    /**
     * 
     * @return string
     */
    public function getAccessToken() {
        return $this->accessToken;
    }
        
    /**
     * 
     * @return string
     */
    public function getResourceName() {
        return $this->resourceName;
    }

    /**
     * 
     * @param string $accessToken
     * @return void
     */
    public function setAccessToken($accessToken) {
        $this->accessToken = $accessToken;
    }
    
    /**
     * 
     * @param ExecutionContextInterface $context
     * @return void
     */
    public function validateAccessToken(ExecutionContextInterface $context) {
        $this->validateAccessTokenWithOauthServer($context);
        $this->validateAccessTokenForResourceOwner($context);
    }
    
    /**
     * 
     * @param Request $request
     * @return string
     */
    protected static function extractAccessToken(Request $request) {
//        $header = $request->headers->get('Authorization');
        $headers = apache_request_headers();        
        $authHeader = isset($headers['Authorization']) ? $headers['Authorization'] : '';
        if (empty($authHeader)) {
            return '';
        }
        
        $credentials = split(' ', $authHeader);
        
        return isset($credentials[1]) ? $credentials[1] : '';
    }
    
    /**
     * 
     * @param ExecutionContextInterface $context
     * @return void
     */
    protected function validateAccessTokenForResourceOwner(ExecutionContextInterface $context) {
        $tokenInfoUrl = static::$OAUTH_TOKEN_INFO_URL . $this->getAccessToken();
        /* @var $response \Buzz\Message\Response */
        $response = $this->browser->get($tokenInfoUrl);
        if ($response->getStatusCode() !== Response::HTTP_OK) {
            return $context->addViolationAt('accessToken', $response->getContent());
        }
        
        /* @var $doctrine \Doctrine\Bundle\DoctrineBundle\Registry */
        $doctrine = $this->container->get('doctrine');
        $resourceRepo = $doctrine->getRepository('MsOauthBundle:Resource');
        /* @var $resource Resource */
        $resource = $resourceRepo->findOneByTitle($this->getResourceName());
        if (empty($resource)) {
            return;
        }
        
        $resourceOwner = $resource->getOwner();
        $responseContent = json_decode($response->getContent(), true);
        $tokenResourceOwner = $responseContent['resource_owner'];
        if ($resourceOwner->getUsername() !== $tokenResourceOwner) {
            $context->addViolationAt(
                'accessToken', 
                'The access token provided has not been issued for the owner of the requested resource.'
            );
        }
    }
    
    /**
     * 
     * @param ExecutionContextInterface $context
     * @return void
     */
    protected function validateAccessTokenWithOauthServer(ExecutionContextInterface $context) {
        $tokenValidationUrl = static::$OAUTH_TOKEN_VALIDATION_URL . $this->getAccessToken();
        /* @var $response \Buzz\Message\Response */
        $response = $this->browser->get($tokenValidationUrl);
        if ($response->getStatusCode() !== Response::HTTP_OK) {
            $context->addViolationAt('accessToken', $response->getContent());
        }
    }
}
