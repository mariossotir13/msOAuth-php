<?php

namespace Ms\OauthBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Description of ClientController
 *
 * @author Marios
 */
class ClientController extends Controller {

    /**
     * 
     */
    public function demo1Action() {
        $url = $this->generateUrl('ms_oauth_authorization');

        return $this->render(
            'MsOauthBundle:Client:demo_1.html.twig', 
            array('url' => $url)
        );
    }

}