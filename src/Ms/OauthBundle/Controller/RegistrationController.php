<?php

namespace Ms\OauthBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Ms\OauthBundle\Entity\Client;
use Ms\OauthBundle\Form\Type\ClientType;
use Symfony\Component\HttpFoundation\Request;
use Ms\OauthBundle\Component\Authentication\AuthenticationServiceInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * 
 */
class RegistrationController extends Controller {

    /**
     * 
     * @return Response
     */
    public function clientAction(Request $request) {
        $client = new Client();
        $form = $this->createForm(new ClientType(), $client);
        $form->handleRequest($request);

        if ($form->isValid()) {
            /* @var $authService AuthenticationServiceInterface */
            $authService = $this->get('ms_oauthbundle_authentication');
            $id = $authService->createClientId($client);
            $secret = $authService->createPassword();
            $encryptionKey = $this->container->getParameter('secret');
            $encryptedSecret = $authService->encryptPassword($secret, $encryptionKey);
            
            $client->setId($id)
                ->setSalt('')
                ->setPassword($encryptedSecret);
            $em = $this->getDoctrine()->getManager();
            $em->persist($client);
            $em->flush();
            
            return $this->redirect(
                $this->generateUrl(
                    'ms_oauth_clientdetails', 
                    array(
                        'id' => $id
                    )
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
     * @param Request $request
     * @param string $id Το Αναγνωριστικό Πελάτη.
     * @return Response
     */
    public function clientDetailsAction($id) {
        $repository = $this->getDoctrine()->getRepository('Ms\OauthBundle\Entity\Client');
        /* @var $client Client */
        $client = $repository->find($id);
        if ($client === null) {
            throw $this->createNotFoundException("could not find client: {$id}");
        }
        
        $secret = $client->getPassword();
        /* @var $authService AuthenticationServiceInterface */
        $authService = $this->get('ms_oauthbundle_authentication');
        $encryptionKey = $this->container->getParameter('secret');
        $decryptedSecret = $authService->decryptPassword($secret, $encryptionKey);
        
        return $this->render(
            'MsOauthBundle:Registration:client_details.html.twig',
            array(
                'client' => $client,
                'secret' => $decryptedSecret
            )
        );
    }
}
