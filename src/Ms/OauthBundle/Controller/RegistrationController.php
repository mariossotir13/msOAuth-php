<?php

namespace Ms\OauthBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Ms\OauthBundle\Entity\Client;
use Ms\OauthBundle\Entity\User;
use Ms\OauthBundle\Form\Type\ClientType;
use \Ms\OauthBundle\Form\Type\ResourceOwnerType;
use Symfony\Component\HttpFoundation\Request;
use Ms\OauthBundle\Component\Authentication\AuthenticationServiceInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Marios
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
                    array('id' => $id)
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
            throw $this->createNotFoundException("Could not find client: {$id}");
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
    
    /**
     * 
     * @return Response
     */
    public function userAction(Request $request) {
        $user = new User();
        $form = $this->createForm(new ResourceOwnerType(), $user);
        $form->handleRequest($request);

        if ($form->isValid()) {
            /* @var $authService AuthenticationServiceInterface */
            $authService = $this->get('ms_oauthbundle_authentication');
            $salt = $authService->createPasswordSalt();
            $user->setSalt($salt);

            /* @var $factory \Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface */
            $factory = $this->get('security.encoder_factory');
            $encoder = $factory->getEncoder($user);
            $password = $encoder->encodePassword($user->getPassword(), $user->getSalt());
            $user->setPassword($password);
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            
            return $this->redirect(
                $this->generateUrl(
                    'ms_oauth_user_details', 
                    array('id' => $user->getId())
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
     * @param string $id Το ID του Ιδιοκτήτη Πόρου.
     * @return Response
     */
    public function userDetailsAction($id) {
        $repository = $this->getDoctrine()->getRepository('Ms\OauthBundle\Entity\User');
        /* @var $user User */
        $user = $repository->find($id);
        if ($user === null) {
            throw $this->createNotFoundException("Could not find user: {$id}");
        }
        
        return $this->render(
            'MsOauthBundle:Registration:user_details.html.twig',
            array('user' => $user)
        );
    }
}
