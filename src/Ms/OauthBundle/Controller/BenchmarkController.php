<?php

namespace Ms\OauthBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Encoder\Pbkdf2PasswordEncoder;
use Symfony\Component\HttpFoundation\Response;

/**
 * Description of BenchmarkController
 *
 * @author user
 */
class BenchmarkController extends Controller {
    
    /**
     * 
     * @return Response
     */
    public function authenticationAction() {
        $targetDiff = 0.2;
        $password = '1';
        $salt = '1';
        $step = 1000;
        $iterations = 1000;
        do {
            $iterations += $step;
            $generator = new Pbkdf2PasswordEncoder('sha512', true, $iterations, 20);
            $startTime = microtime(true);
            $generator->encodePassword($password, $salt);
            $endTime = microtime(true);
        } while ( ($endTime - $startTime) < $targetDiff );
        $iterations -= $step;
        
        return $this->render(
            'MsOauthBundle:Benchmark:authentication.html.twig',
            array('iterations' => $iterations)
        );
    }
}
