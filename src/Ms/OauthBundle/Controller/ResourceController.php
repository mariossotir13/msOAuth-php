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
     * @return void | JsonResponse
     */
    public function imageAction(Request $request, $name) {
        $accessRequest = AccessRequest::fromRequest($request, $this->container->get('buzz'));
        $validationResponse = $this->validateAccessRequest($accessRequest);
        if (!$validationResponse->isValid()) {
            return new JsonResponse(
                array(
                    'error' => $validationResponse->getError(),
                    'error_message' => $validationResponse->getErrorMessage()
                ),
                JsonResponse::HTTP_UNAUTHORIZED
            );
        }
        
        $resource = $this->findResource($name);
        if (empty($resource)) {
            throw $this->createNotFoundException('Could not find image: ' . $name);
        }
        
        $this->loadFile($resource);
        
        exit;
    }

    /**
     * 
     * @param Request $request
     * @param string $name
     * @return void | JsonResponse
     */
    public function imageGroup(Request $request, $name) {
        $accessRequest = AccessRequest::fromRequest($request, $this->get('buzz'));
        $validationResponse = $this->validateAccessRequest($accessRequest);
        if (!$validationResponse->isValid()) {
            return new JsonResponse(
                array(
                    'error' => $validationResponse->getError(),
                    'error_message' => $validationResponse->getErrorMessage()
                ),
                JsonResponse::HTTP_UNAUTHORIZED
            );
        }
        
        $repository = $this->getDoctrine()->getRepository('MsOauthBundle:ResourceGroup');
        $group = $repository->findOneByTitle($name);
        if ($group === null) {
            throw $this->createNotFoundException('Could not find image group: ' . $name);
        }
        
        $this->loadFilesOfGroup($group);
        
        exit;
    }

    /**
     * 
     * @param string $name
     * @return Response
     */
    public function resourceAction($name) {
        $resource = $this->findResource($name);
        if(empty($resource)) {
            return $this->createNotFoundException('Could not find resource: ' . $name);
        }
        
        $response = new Response($resource->getContent());
        $response->headers->set('Content-Type', $resource->getMimeType());
        
        return $response;
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
     * Η υλοποίηση βασίζεται στο εγχειρίδιο χρήσης της συνάρτησης [readfile][1].
     * 
     * 
     * [1]: http://www.php.net/readfile
     *      "PHP: readfile - Manual"
     * 
     * @param Resource $resource
     * @return void
     */
    protected function loadFile(Resource $resource) {
        header('Content-Type: ' . $resource->getMimeType());
        ob_clean();
        flush();
        
        $path = realpath(static::$WEB_ROOT . $resource->getContent());
        $fileInfo = new \SplFileInfo($path);
        if ($fileInfo->isFile() 
                && $fileInfo->isReadable()) {
            readfile($fileInfo->getRealPath());
        }
    }
    
    /**
     * 
     * @param ResourceGroup $group
     * @return void
     * @see #loadFile
     */
    protected function loadFilesOfGroup(ResourceGroup $group) {
        $images = $group->getResources();
        foreach ($images as $image) {
//            $this->loadFile($image);
        }
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

