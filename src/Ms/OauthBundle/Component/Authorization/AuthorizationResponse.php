<?php

namespace Ms\OauthBundle\Component\Authorization;

/**
 * Description of AuthorizationResponse
 *
 * @author Marios
 */
class AuthorizationResponse {
    
    /**#@+
     * @var string
     */
    private static $OAUTH_CODE = 'code';
    private static $STATE = 'state';
    /**#@-*/
    
    /**
     *
     * @var string
     */
    private $oauthCode;
    
    /**
     *
     * @var string
     */
    private $redirectionUri;
    
    /**
     *
     * @var string
     */
    private $state = '';
    
    /**
     * 
     * @param string $redirectionUri Το URI Ανακατεύθυνσης.
     * @param string $authorizationCode Ο κωδικός εξουσιοδότησης.
     * @throws \InvalidArgumentException εάν οποιαδήποτε από τις παραμέτρους
     * είναι *null*.
     */
    public function __construct($redirectionUri, $authorizationCode) {
        if ($redirectionUri === null) {
            throw new \InvalidArgumentException('No redirection URI was specified.');
        }
        if ($authorizationCode === null) {
            throw new \InvalidArgumentException('No authorization code was specified.');
        }
        $this->redirectionUri = $redirectionUri;
        $this->oauthCode = $authorizationCode;
    }
    
    /**
     * 
     * @return string
     */
    public function getOauthCode() {
        return $this->oauthCode;
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
    public function getState() {
        return $this->state;
    }

    /**
     * 
     * @param string $state
     * @throws \InvalidArgumentException
     */
    public function setState($state) {
        if ($state === null) {
            throw new \InvalidArgumentException('No state was specified.');
        }
        $this->state = $state;
    }

    /**
     * Επιστρέφει μία αναπαράσταση του παρόντος αντικειμένου κατάλληλη για προσθήκη
     * σε ένα [Query String][1].
     * 
     * 
     * [1]: http://en.wikipedia.org/wiki/Query_string
     *      "Query String στη Wikipedia"
     * 
     * @return string Η URI αναπαράσταση του παρόντος αντικειμένου.
     */
    public function toQueryString() {
        $queryStringArr = array(static::$OAUTH_CODE => $this->oauthCode);
        if (!empty($this->state)) {
            $queryStringArr[static::$STATE] = $this->state;
        }
        
        return http_build_query($queryStringArr, '', '&');
    }
    
    /**
     * @return string
     */
    public function toUri() {
        return $this->redirectionUri . '?' . $this->toQueryString();
    }
}