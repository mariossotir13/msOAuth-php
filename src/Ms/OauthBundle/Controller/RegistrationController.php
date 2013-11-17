<?php

namespace Ms\OauthBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Ms\OauthBundle\Entity\Client;
use Ms\OauthBundle\Form\Type\ClientType;
use Symfony\Component\HttpFoundation\Request;
use Ms\OauthBundle\Component\Authentication\MsOauthAuthenticationService;

/**
 * 
 */
class RegistrationController extends Controller {

    /**
     * 
     * @return type
     */
    public function clientAction(Request $request) {
        $client = new Client();
        $form = $this->createForm(new ClientType(), $client);
        $form->handleRequest($request);

        if ($form->isValid()) {
            // TODO: Generate user password.
            /* @var $authService MsOauthAuthenticationService */
            $authService = $this->get('ms_oauthbundle_authentication');
            $id = $authService->createClientId($client);
            
            $client->setId($id)
                ->setPassword('1');
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($client);
            $em->flush();
        }

        return $this->render(
            'MsOauthBundle:Registration:client.html.twig', 
            array('form' => $form->createView())
        );
    }

}
