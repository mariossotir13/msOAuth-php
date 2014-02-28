<?php

namespace Ms\OauthBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Ms\OauthBundle\Entity\Resource;
use Ms\OauthBundle\Entity\ResourceGroup;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Ms\OauthBundle\Component\Access\AccessRequest;
use Ms\OauthBundle\Component\Authorization\ValidationResponse;
use Ms\OauthBundle\Component\Authorization\AccessTokenErrorResponse;
use Ms\OauthBundle\Entity\ResourceOwner;

/**
 * Description of ResourceController
 *
 * @author Marios
 */
class ResourceController extends Controller {

    /**
     *
     * @var string
     */
    protected static $WEB_ROOT = './bundles/msdemo/';

    /**
     * 
     * @param Request $request
     * @param string $name
     * @return JsonResponse|Response
     */
    public function imageAction(Request $request, $name) {
        $accessRequest = AccessRequest::fromRequest(
            $request, 
            $this->container->get('buzz'), 
            $this->container
        );
        $validationResponse = $this->validateAccessRequest($accessRequest, $request);
        if (!$validationResponse->isValid()) {
            return $this->createInvalidRequestResponse($validationResponse);
        }

        $resource = $this->findResource($name);
        if (empty($resource)) {
            return new JsonResponse(
                array(
                    AccessTokenErrorResponse::ERROR => 'invalid_resource',
                    AccessTokenErrorResponse::ERROR_DESCRIPTION => 'Could not find image: ' . $name
                ), 
                JsonResponse::HTTP_NOT_FOUND
            );
        }

        $response = new Response(
            $this->loadFile($resource), 
            Response::HTTP_OK, 
            array('Content-Type' => $resource->getMimeType())
        );
        $response->setMaxAge(3600);

        return $response;
    }

    /**
     * 
     * @param Request $request
     * @param string $name
     * @return JsonResponse
     */
    public function imageGroupAction(Request $request, $name) {
        $accessRequest = AccessRequest::fromRequest(
                $request, $this->get('buzz'), $this->container
        );
        $validationResponse = $this->validateAccessRequest($accessRequest, $request);
        if (!$validationResponse->isValid()) {
            return $this->createInvalidRequestResponse($validationResponse);
        }

        $repository = $this->getDoctrine()->getRepository('MsOauthBundle:ResourceGroup');
        $group = $repository->findOneByTitle($name);
        if ($group === null) {
            return new JsonResponse(
                array(
                    AccessTokenErrorResponse::ERROR => 'invalid_resource',
                    AccessTokenErrorResponse::ERROR_DESCRIPTION => 'Could not find image group: ' . $name
                ), 
                JsonResponse::HTTP_NOT_FOUND
            );
        }

        return new JsonResponse(
            array('image_titles' => $this->getImageTitlesOfGroup($group))
        );
    }

    /**
     * 
     * @param string $name
     * @return Response
     * @deprecated
     */
    public function resourceAction($name) {
        $resource = $this->findResource($name);
        if (empty($resource)) {
            throw $this->createNotFoundException('Could not find resource: ' . $name);
        }

        $response = new Response($resource->getContent());
        $response->headers->set('Content-Type', $resource->getMimeType());

        return $response;
    }

    /**
     * 
     */
    public function userProfileAction() {
        /* @var $user \Ms\OauthBundle\Entity\ResourceOwner */
        $user = $this->get('security.context')->getToken()->getUser();
        $groups = $user->getResourceGroups();
        $images = $this->getResourceOwnerImages($user);

        return $this->render(
            'MsOauthBundle:Resource:user_profile.html.twig', 
            array(
                'image_groups' => $groups,
                'images' => $images,
                'logout_url' => $this->generateUrl('ms_oauth_authentication_user_logout'),
                'logout_title' => 'Log out',
                'username' => $user->getUsername()
            )
        );
    }

    /**
     * 
     * @param ValidationResponse $validationResponse
     * @return JsonResponse
     */
    protected function createInvalidRequestResponse(ValidationResponse $validationResponse) {
        return new JsonResponse(
            array(
            AccessTokenErrorResponse::ERROR => $validationResponse->getError(),
            AccessTokenErrorResponse::ERROR_DESCRIPTION => $validationResponse->getErrorMessage()
            ), JsonResponse::HTTP_UNAUTHORIZED
        );
    }

    /**
     * 
     * @param string $name
     * @return Resource
     */
    protected function findResource($name) {
        /* @var $repository \Doctrine\Common\Persistence\ObjectRepository */
        $repository = $this->getDoctrine()->getRepository('Ms\OauthBundle\Entity\Resource');

        return $repository->findOneByTitle($name);
    }

    /**
     * 
     * @param ResourceGroup $group
     * @return string[]
     */
    protected function getImageTitlesOfGroup(ResourceGroup $group) {
        $titles = array();
        $images = $group->getResources();
        /* @var $image Resource */
        foreach ($images as $image) {
            $titles[] = $image->getTitle();
        }

        return $titles;
    }
    
    /**
     * 
     * @param ResourceOwner $owner
     * @return array
     */
    protected function getResourceOwnerImages(ResourceOwner $owner) {
        $images = array();
        $resourceGroups = $owner->getResourceGroups();
        /* @var $group ResourceGroup */
        foreach ($resourceGroups as $group) {
            $images[ $group->getTitle() ] = $group->getResources();
        }
        
        return $images;
    }

    /**
     * 
     * @param Resource $resource
     * @return string
     */
    protected function loadFile(Resource $resource) {
        $path = realpath(static::$WEB_ROOT . $resource->getContent());
        $fileInfo = new \SplFileInfo($path);
        if (!$fileInfo->isFile() || !$fileInfo->isReadable()) {
            return '';
        }

        return file_get_contents($fileInfo->getRealPath());
    }

    /**
     * 
     * @param AccessRequest $request
     * @param Request $symfonyRequest
     * @return ValidationResponse
     */
    protected function validateAccessRequest(AccessRequest $request, Request $symfonyRequest) {
        /* @var $validator \Symfony\Component\Validator\Validator */
        $validator = $this->get('validator');
        
        if (preg_match('#oauth2/u/profile#', $symfonyRequest->headers->get('referer'))) {
            /* @var $violations \Symfony\Component\Validator\ConstraintViolationListInterface */
            $violations = $validator->validate($request, array('Resource'));
        } else {
            /* @var $violations \Symfony\Component\Validator\ConstraintViolationListInterface */
            $violations = $validator->validate($request);
        }

        return new ValidationResponse(
            $violations, array(
            'accessToken' => 'invalid_token',
            'resourceName' => 'invalid_request'
            )
        );
    }

}
