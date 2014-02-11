<?php

namespace Ms\DemoBundle\Component\Ms\Oauth;

use Ms\OauthBundle\Component\Authorization\AccessTokenRequest;
use Ms\OauthBundle\Component\Authorization\AuthorizationRequest;
use Ms\OauthBundle\Entity\AuthorizationCodeScope;
use Ms\OauthBundle\Component\Authorization\AuthorizationResponseType;

/**
 * Description of RequestGenerator
 *
 * @author Marios
 */
class RequestGenerator {
    
    /**@#+
     *
     * @var string
     */
    private static $AUTHORIZATION_RESPONSE_TYPE_UNSUPPORTED = 'c';
    private static $CLIENT_ID = 'zMuobKhbnvJUTYc+EnXfRwiiHP4/OpmM5CLrdpkIsm4';
    private static $CLIENT_ID_INVALID = 'z';
    private static $CLIENT_ID_WRONG = 'z';
    private static $GRANT_TYPE_UNSUPPORTED = 'ac';
    private static $REDIRECTION_URI = 'http://msoauthphp.local/app_dev.php/client-app/demo1';
    private static $REDIRECTION_URI_INVALID = 'http://msoauthphp.local/app_dev.php/client-app/demo1#a';
    private static $PASSWORD = 'fmodKwVrRQOC2Io7TpWu0VDkTrA=';
    private static $SCOPE_INVALID = 'c';
    private static $STATE = 'RdoTKJnaUxdRfE7QBTZX';
    /**@#-*/
    
    /**
     * 
     * @param string $code
     * @param boolean $asUri
     * @return AccessTokenRequest
     */
    public function createAccessTokenRequest($code, $asUri = false) {
        $request = new AccessTokenRequest(AccessTokenRequest::SERVER_URI);
        $request->setClientId(static::$CLIENT_ID);
        $request->setCode($code);
        $request->setGrantType('authorization_code');
        $request->setRedirectionUri(static::$REDIRECTION_URI);
        
        return $asUri ? $request->toUri(true) : $request;
    }
    
    /**
     * 
     * @param string $code
     * @param boolean $asUri
     * @return AccessTokenRequest
     */
    public function createAccessTokenRequestWithInvalidRedirectionUri($code, $asUri = false) {
        $request = new AccessTokenRequest(AccessTokenRequest::SERVER_URI);
        $request->setClientId(static::$CLIENT_ID);
        $request->setCode($code);
        $request->setGrantType('authorization_code');
        $request->setRedirectionUri(static::$REDIRECTION_URI_INVALID);
        
        return $asUri ? $request->toUri(true) : $request;
    }
    
    /**
     * 
     * @param string $code
     * @param boolean $asUri
     * @return AccessTokenRequest
     */
    public function createAccessTokenRequestWithMissingRequiredParameter($code, $asUri = false) {
        $request = new AccessTokenRequest(AccessTokenRequest::SERVER_URI);
        $request->setClientId(static::$CLIENT_ID);
        $request->setCode($code);
        $request->setRedirectionUri(static::$REDIRECTION_URI);
        
        return $asUri ? $request->toUri(true) : $request;
    }
    
    /**
     * 
     * @param string $code
     * @param boolean $asUri
     * @return AccessTokenRequest
     */
    public function createAccessTokenRequestWithUnsupportedGrantType($code, $asUri = false) {
        $request = new AccessTokenRequest(AccessTokenRequest::SERVER_URI);
        $request->setClientId(static::$CLIENT_ID);
        $request->setCode($code);
        $request->setGrantType(static::$GRANT_TYPE_UNSUPPORTED);
        $request->setRedirectionUri(static::$REDIRECTION_URI);
        
        return $asUri ? $request->toUri(true) : $request;
    }
    
    /**
     * 
     * @param string $code
     * @param boolean $asUri
     * @return AccessTokenRequest
     */
    public function createAccessTokenRequestWithWrongClientId($code, $asUri = false) {
        $request = new AccessTokenRequest(AccessTokenRequest::SERVER_URI);
        $request->setClientId(static::$CLIENT_ID_WRONG);
        $request->setCode($code);
        $request->setGrantType('authorization_code');
        $request->setRedirectionUri(static::$REDIRECTION_URI);
        
        return $asUri ? $request->toUri(true) : $request;
    }
    
    /**
     * 
     * @param string $code
     * @param boolean $asUri
     * @return AccessTokenRequest
     */
    public function createAccessTokenRequestWithMissingRedirectionUri($code, $asUri = false) {
        $request = new AccessTokenRequest(AccessTokenRequest::SERVER_URI);
        $request->setClientId(static::$CLIENT_ID);
        $request->setCode($code);
        $request->setGrantType('authorization_code');
        
        return $asUri ? $request->toUri(true) : $request;
    }
    
    /**
     * 
     * @param boolean $asUri
     * @return string | AuthorizationRequest
     */
    public function createAuthorizationRequest($asUri = false) {
        $request = new AuthorizationRequest(AuthorizationRequest::SERVER_URI);
        $request->addScope(AuthorizationCodeScope::BASIC);
        $request->setClientId(static::$CLIENT_ID);
        $request->setRedirectionUri(static::$REDIRECTION_URI);
        $request->setResponseType(AuthorizationResponseType::CODE);
        $request->setState(static::$STATE);
        
        return $asUri ? $request->toUri() : $request;
    }
    
    /**
     * 
     * @param boolean $asUri
     * @return string | AuthorizationRequest
     */
    public function createAuthorizationRequestWithInvalidClientId($asUri = false) {
        $request = new AuthorizationRequest(AuthorizationRequest::SERVER_URI);
        $request->addScope(AuthorizationCodeScope::BASIC);
        $request->setClientId(static::$CLIENT_ID_INVALID);
        $request->setRedirectionUri(static::$REDIRECTION_URI);
        $request->setResponseType(AuthorizationResponseType::CODE);
        $request->setState(static::$STATE);
        
        return $asUri ? $request->toUri() : $request;
    }
    
    /**
     * 
     * @param boolean $asUri
     * @return string | AuthorizationRequest
     */
    public function createAuthorizationRequestWithInvalidRedirectionUri($asUri = false) {
        $request = new AuthorizationRequest(AuthorizationRequest::SERVER_URI);
        $request->addScope(AuthorizationCodeScope::BASIC);
        $request->setClientId(static::$CLIENT_ID);
        $request->setRedirectionUri(static::$REDIRECTION_URI_INVALID);
        $request->setResponseType(AuthorizationResponseType::CODE);
        $request->setState(static::$STATE);
        
        return $asUri ? $request->toUri() : $request;
    }
    
    /**
     * 
     * @param boolean $asUri
     * @return string | AuthorizationRequest
     */
    public function createAuthorizationRequestWithInvalidScope($asUri = false) {
        $request = new AuthorizationRequest(AuthorizationRequest::SERVER_URI);
        $request->addScope(static::$SCOPE_INVALID);
        $request->setClientId(static::$CLIENT_ID);
        $request->setRedirectionUri(static::$REDIRECTION_URI);
        $request->setResponseType(AuthorizationResponseType::CODE);
        $request->setState(static::$STATE);
        
        return $asUri ? $request->toUri() : $request;
    }
    
    /**
     * 
     * @param boolean $asUri
     * @return string | AuthorizationRequest
     */
    public function createAuthorizationRequestWithMissingRedirectionUri($asUri = false) {
        $request = new AuthorizationRequest(AuthorizationRequest::SERVER_URI);
        $request->addScope(AuthorizationCodeScope::BASIC);
        $request->setClientId(static::$CLIENT_ID);
        $request->setResponseType(AuthorizationResponseType::CODE);
        $request->setState(static::$STATE);
        
        return $asUri ? $request->toUri() : $request;
    }
    
    /**
     * 
     * @param boolean $asUri
     * @return string | AuthorizationRequest
     */
    public function createAuthorizationRequestWithMissingRequiredParameter($asUri = false) {
        $request = new AuthorizationRequest(AuthorizationRequest::SERVER_URI);
        $request->addScope(AuthorizationCodeScope::BASIC);
        $request->setRedirectionUri(static::$REDIRECTION_URI);
        $request->setResponseType(AuthorizationResponseType::CODE);
        $request->setState(static::$STATE);
        
        return $asUri ? $request->toUri() : $request;
    }
    
    /**
     * 
     * @param boolean $asUri
     * @return string | AuthorizationRequest
     */
    public function createAuthorizationRequestWithUnsupportedResponseType($asUri = false) {
        $request = new AuthorizationRequest(AuthorizationRequest::SERVER_URI);
        $request->addScope(AuthorizationCodeScope::BASIC);
        $request->setClientId(static::$CLIENT_ID);
        $request->setRedirectionUri(static::$REDIRECTION_URI);
        $request->setResponseType(static::$AUTHORIZATION_RESPONSE_TYPE_UNSUPPORTED);
        $request->setState(static::$STATE);
        
        return $asUri ? $request->toUri() : $request;
    }
}
