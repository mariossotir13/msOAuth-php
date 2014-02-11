<?php

namespace Ms\DemoBundle\Component\Ms\Oauth;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Ms\DemoBundle\Component\Ms\Oauth\RequestGenerator;
use Symfony\Component\HttpFoundation\Response;
use Buzz\Browser;
use Buzz\Message\Response as BuzzResponse;

/**
 * Description of OauthMediator
 *
 * @author Marios
 */
class OauthMediator {
    
    /**
     *
     * @var string[]
     */
    private static $RESPONSE_HEADERS = array(
        'Authorization',
        'Content-Type',
        'WWW-Authenticate'
    );
    
    /**
     *
     * @var Controller
     */
    private $controller;
    
    /**
     *
     * @var RequestGenerator
     */
    private $requestGenerator;
    
    /**
     * 
     * @param Controller $controller
     */
    function __construct(Controller $controller) {
        $this->controller = $controller;
        $this->requestGenerator = new RequestGenerator();
    }
    
    /**
     * Επιστρέφει `true` όταν η `$response` δείχνει πως απαιτείται αυθεντικοποίηση.
     * 
     * Π.χ., όταν ο Πελάτης αιτείται *κωδικό εξουσιοδότησης*, ο Εξυπηρετητής
     * Εξουσιοδοτήσεων μπορεί να επιστρέψει μία τέτοια απάντηση, έτσι ώστε να
     * παρακινήσει τον Πελάτη να αυθεντικοποιηθεί.
     * 
     * @param Response $response
     * @return bool
     */
    public function isUnauthenticatedResponse(Response $response) {
        return $response->headers->has('WWW-Authenticate');
    }
    
    /**
     * Επιστρέφει `true` όταν η `$response` δείχνει πως η αίτηση δεν ικανοποιήθηκε
     * γιατί απέτυχε να παρουσιάσει τα απαραίτητα διαπιστευτήρια εξουσιοδότησης.
     * 
     * Π.χ., όταν ο Πελάτης αιτείται έναν Πόρο από έναν Εξυπηρετητή Πόρων ο οποίος
     * υλοποιεί το πρωτόκολλο OAuth και δεν αποστέλλει κάποιο *τεκμήριο πρόσβασης*,
     * τότε η `$response` θα έχει ως αποτέλεσμα αυτή η μέθοδος να επιστρέψει `true`.
     * 
     * @param Response $response
     * @return bool
     */
    public function isUnauthorizedResponse(Response $response) {
        return $response->getStatusCode() === Response::HTTP_UNAUTHORIZED;
    }
    
    /**
     * 
     * @return Response
     */
    public function requestAccessToken() {
        $response = $this->requestAuthorizationCode();
        
        return $response;
    }
    
    /**
     * 
     * @return Response
     */
    public function requestAuthorizationCode() {
        $authorizationCodeRequestUrl = $this->requestGenerator->createAuthorizationRequest(true);
//        $browser = $this->getBrowser();
//        $response = $browser->get($authorizationCodeRequestUrl);
//        
//        return $this->transformBuzzToSymfonyResponse($response);
        
        return $this->controller->redirect($authorizationCodeRequestUrl);
    }
    
    /**
     * 
     * @return Browser
     */
    protected function getBrowser() {
        /* @var $browser Browser */
        $browser = $this->controller->get('buzz');
        
        /* @var $client \Buzz\Client\Curl */
        $client = $browser->getClient();
        $client->setOption(CURLOPT_CONNECTTIMEOUT, 400);
        $client->setOption(CURLOPT_TIMEOUT, 400);
        $browser->setClient($client);
        
        return $browser;
    }
    
    /**
     * 
     * @param BuzzResponse $response
     * @return Response
     */
    protected function transformBuzzToSymfonyResponse(BuzzResponse $response) {
        $headers = array();
        foreach (static::$RESPONSE_HEADERS as $name) {
            if ($response->getHeader($name)) {
                $headers[$name] = $response->getHeader($name);
            }
        }
        
        return new Response($response->getContent(), $response->getStatusCode(), $headers);
    }
}
