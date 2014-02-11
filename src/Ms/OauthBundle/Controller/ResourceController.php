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
        $accessRequest = AccessRequest::fromRequest($request, $this->container->get('buzz'));
        $validationResponse = $this->validateAccessRequest($accessRequest);
        if (!$validationResponse->isValid()) {
            return $this->createInvalidRequestResponse($validationResponse);
        }
        
        $resource = $this->findResource($name);
        if (empty($resource)) {
            throw $this->createNotFoundException('Could not find image: ' . $name);
        }
        
        return new Response(
            $this->loadFile($resource),
            Response::HTTP_OK,
            array(
                'Content-Type' => $resource->getMimeType()
            )
        );
    }

    /**
     * 
     * @param Request $request
     * @param string $name
     * @return JsonResponse
     */
    public function imageGroupAction(Request $request, $name) {
        $accessRequest = AccessRequest::fromRequest($request, $this->get('buzz'));
        $validationResponse = $this->validateAccessRequest($accessRequest);
        if (!$validationResponse->isValid()) {
            return $this->createInvalidRequestResponse($validationResponse);
        }
        
        $repository = $this->getDoctrine()->getRepository('MsOauthBundle:ResourceGroup');
        $group = $repository->findOneByTitle($name);
        if ($group === null) {
            throw $this->createNotFoundException('Could not find image group: ' . $name);
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
        if(empty($resource)) {
            throw $this->createNotFoundException('Could not find resource: ' . $name);
        }
        
        $response = new Response($resource->getContent());
        $response->headers->set('Content-Type', $resource->getMimeType());
        
        return $response;
    }
    
    /**
     * 
     * @param ValidationResponse $validationResponse
     * @return JsonResponse
     */
    protected function createInvalidRequestResponse(ValidationResponse $validationResponse) {
        return new JsonResponse(
            array(
                'error' => $validationResponse->getError(),
                'error_message' => $validationResponse->getErrorMessage()
            ),
            JsonResponse::HTTP_UNAUTHORIZED
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
     * @param Resource $resource
     * @return string
     */
    protected function loadFile(Resource $resource) {
        $path = realpath(static::$WEB_ROOT . $resource->getContent());
        $fileInfo = new \SplFileInfo($path);
        if (!$fileInfo->isFile() 
                || !$fileInfo->isReadable()) {
            return '';
        }
        
        return file_get_contents($fileInfo->getRealPath());
    }
    
    /**
     * 
     * @param AccessRequest $request
     * @return ValidationResponse
     */
    protected function validateAccessRequest(AccessRequest $request) {
        /* @var $validator \Symfony\Component\Validator\Validator */
        $validator = $this->get('validator');
        /* @var $violations \Symfony\Component\Validator\ConstraintViolationListInterface */
        $violations = $validator->validate($request);
        
        return new ValidationResponse(
            $violations,
            array(
                'accessToken' => 'invalid_token',
                'resourceName' => 'invalid_request'
            )
        );
    }
}

