<?php

namespace Ms\OauthBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Ms\OauthBundle\Entity\Client;
use Ms\OauthBundle\Form\Type\ClientType;
use Symfony\Component\HttpFoundation\Request;
use Ms\OauthBundle\Component\Authentication\AuthenticationServiceInterface;

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
            /* @var $authService AuthenticationServiceInterface */
            $authService = $this->get('ms_oauthbundle_authentication');
            $id = trim($authService->createClientId($client), '=');
            $passwordSalt = $authService->createPasswordSalt();
            $password = $authService->createPassword($passwordSalt);
            
            $client->setId($id)
                ->setSalt($passwordSalt)
                ->setPassword($password);
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($client);
            $em->flush();
            
            return $this->redirect(
                "/client/{$id}"
            );
        }

        return $this->render(
            'MsOauthBundle:Registration:client.html.twig', 
            array('form' => $form->createView())
        );
    }
    
    /**
     * 
     * @param string $id Το Αναγνωριστικό Πελάτη.
     */
    public function clientDetailsAction($id) {
        $repository = $this->getDoctrine()->getRepository('Ms\OauthBundle\Entity\Client');
        $client = $repository->find($id);
        
        return $this->render(
            'MsOauthBundle:Registration:client_details.html.twig',
            array(
                'client' => $client
            )
        );
    }
}
