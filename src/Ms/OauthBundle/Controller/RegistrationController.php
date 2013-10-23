<?php

namespace Ms\OauthBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class RegistrationController extends Controller {

    public function clientAction() {
        return $this->render(
            'MsOauthBundle:Registration:client.html.twig'
        );
    }
}