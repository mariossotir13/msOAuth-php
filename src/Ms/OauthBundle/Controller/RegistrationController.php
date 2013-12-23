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
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function clientAction(Request $request) {
        $client = new Client();
        $form = $this->createForm(new ClientType(), $client);
        $form->handleRequest($request);

        if ($form->isValid()) {
            /* @var $authService AuthenticationServiceInterface */
            $authService = $this->get('ms_oauthbundle_authentication');
            $id = $authService->createClientId($client);
            $passwordSalt = $authService->createPasswordSalt();
            $password = $authService->createPassword();
            $hashedPassword = $authService->hashPassword($password, $passwordSalt);
            
            $client->setId($id)
                ->setSalt($passwordSalt)
                ->setPassword($hashedPassword);
            $em = $this->getDoctrine()->getManager();
            $em->persist($client);
            $em->flush();
            
            return $this->redirect(
                $this->generateUrl(
                    'ms_oauth_clientdetails', 
                    array('id' => urlencode($id))
                )
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
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function clientDetailsAction($id) {
        $repository = $this->getDoctrine()->getRepository('Ms\OauthBundle\Entity\Client');
        $client = $repository->find(urldecode($id));
        if ($client === null) {
            throw $this->createNotFoundException("could not find client: {$id}");
        }
        
        return $this->render(
            'MsOauthBundle:Registration:client_details.html.twig',
            array(
                'client' => $client
            )
        );
    }
}
