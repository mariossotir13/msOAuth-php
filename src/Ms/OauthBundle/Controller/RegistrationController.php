<?php

namespace Ms\OauthBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Ms\OauthBundle\Entity\User;
use Ms\OauthBundle\Form\Type\UserType;
use Symfony\Component\HttpFoundation\Request;

/**
 * 
 */
class RegistrationController extends Controller {

    /**
     * 
     * @return type
     */
    public function clientAction(Request $request) {
        $user = new User();
        // TODO: Generate user ID and password.
        $user->setId('1')
            ->setPassword('1');
        $form = $this->createForm(new UserType(), $user);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($user);
            $em->flush();
        }
        
        return $this->render(
            'MsOauthBundle:Registration:client.html.twig', 
            array('form' => $form->createView())
        );
    }

}
