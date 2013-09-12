<?php

namespace Ms\OauthBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class RegistrationController extends Controller {

    public function clientAction($name) {
        return $this->render(
            'MsOathBundle:Registration:client.html.twig'
        );
    }

}

?>
