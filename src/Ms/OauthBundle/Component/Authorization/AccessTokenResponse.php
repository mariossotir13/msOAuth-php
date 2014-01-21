<?php

namespace Ms\OauthBundle\Component\Authorization;

use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\Common\Collections\Collection;

/**
 * Description of AccessTokenResponse
 *
 * @author Marios
 */
class AccessTokenResponse extends JsonResponse {
    
    /**@#+
     * 
     * @var string
     */
    private static $TOKEN = 'access_token';
    private static $EXPIRES_IN = 'expires_in';
    private static $SCOPE = 'scope';
    private static $TOKEN_TYPE = 'token_type';
    /**@#-*/
    
    /**
     *
     * @var string
     */
    private $accessToken = '';
    
    /**
     *
     * @var string
     */
    private $accessTokenType = '';

    /**
     *
     * @var int
     */
    private $expiresIn = 0;
    
    /**
     *
     * @var Collection
     */
    private $scopes = null;
    
    /**
     * 
     * @param int $expiresIn
     * @param Collection $scopes
     * @param string $accessToken
     * @param string $accessTokenType
     * @throws \InvalidArgumentException εάν κάποιο από τα ορίσματα είναι *άδειο*
     * ή το όρισμα `$expiresIn` δεν είναι φυσικός αριθμός.
     */
    function __construct($expiresIn, Collection $scopes, $accessToken, $accessTokenType) {
        if (!is_int($expiresIn)
                || $expiresIn <= 0) {
            throw new \InvalidArgumentException('The expiration limit should be a natural number.');
        }
        if (empty($scopes)) {
            throw new \InvalidArgumentException('No scopes were specified.');
        }
        if (empty($accessToken)) {
            throw new \InvalidArgumentException('No access token was specified.');
        }
        if (empty($accessTokenType)) {
            throw new \InvalidArgumentException('No access token type was specified.');
        }
        parent::__construct();
        
        $this->expiresIn = $expiresIn;
        $this->scopes = $scopes;
        $this->accessToken = $accessToken;
        $this->accessTokenType = $accessTokenType;
        
        $this->setUpContent();
    }

    /**
     * 
     * @return int
     */
    public function getExpiresIn() {
        return $this->expiresIn;
    }

    /**
     * 
     * @return Collection
     */
    public function getScopes() {
        return $this->scopes;
    }

    /**
     * 
     * @return string
     */
    public function getToken() {
        return $this->accessToken;
    }

    /**
     * 
     * @return string
     */
    public function getTokenType() {
        return $this->accessTokenType;
    }

    /**
     * @return void
     */
    protected function setUpContent() {
        $contentArr = array(
            static::$TOKEN => $this->getToken(),
            static::$EXPIRES_IN => $this->getExpiresIn(),
            static::$SCOPE => $this->formatScopesForQueryString($this->getScopes()),
            static::$TOKEN_TYPE => $this->getTokenType()
        );
        $this->setData(json_encode($contentArr));
    }
    
    /**
     * @param Collection $scopes
     * @return string
     */
    protected function formatScopesForQueryString(Collection $scopes) {
        $arr = array();
        /* @var $scope \Ms\OauthBundle\Entity\AuthorizationCodeScope */
        foreach ($scopes as $scope) {
            $arr[] = $scope->getTitle();
        }
        
        return implode(' ', $arr);
    }
}
