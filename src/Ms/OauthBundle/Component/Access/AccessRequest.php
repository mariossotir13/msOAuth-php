<?php

namespace Ms\OauthBundle\Component\Access;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ExecutionContextInterface;
use Buzz\Browser;
use Symfony\Component\HttpFoundation\Response;

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
    
    /**
     *
     * @var string
     */
    protected static $OAUTH_TOKEN_VALIDATION_URL = 
        'http://msoauthphp.local/app_dev.php/oauth2/c/accessToken/validation/';
    
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
     * @var string
     */
    private $resourceName;
    
    /**
     * 
     * @param Request $request
     * @param Browser $browser
     * @return AccessRequest
     * @throws \InvalidArgumentException
     */
    public static function fromRequest(Request $request, Browser $browser) {
        if ($request === null) {
            throw new \InvalidArgumentException('No request was provided.');
        }
        
        $accessRequest = new AccessRequest($request->get(self::PARAM_NAME), $browser);
        $accessRequest->setAccessToken(static::extractAccessToken($request));
        
        return $accessRequest;
    }

    /**
     * 
     * @param string $resourceName
     * @param Browser $browser
     */
    function __construct($resourceName, Browser $browser) {
        if ($browser === null) {
            throw new \InvalidArgumentException('No browser was provided.');
        }
        $this->resourceName = $resourceName;
        $this->browser = $browser;
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
        $tokenValidationUrl = static::$OAUTH_TOKEN_VALIDATION_URL . urlencode($this->getAccessToken());
        $response = $this->browser->get($tokenValidationUrl);
        
        if ($response->getHeader('Status Code') !== Response::HTTP_OK) {
            $context->addViolationAt(
                'accessToken',
                'OAuth server found the token "' . $this->getAccessToken() . '" to be invalid.'
            );
        }
    }
    
    /**
     * 
     * @param Request $request
     * @return string
     */
    protected static function extractAccessToken(Request $request) {
        $header = $request->headers->get('Authorization');
        if (empty($header)) {
            return '';
        }
        
        $credentials = split(' ', $header);
        
        return isset($credentials[1]) ? $credentials[1] : '';
    }
}
