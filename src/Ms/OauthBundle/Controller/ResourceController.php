<?php

namespace Ms\OauthBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Ms\OauthBundle\Entity\Resource;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Buzz\Browser;

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
     * @return void
     */
    public function imageAction(Request $request, $name) {
        $valid = $this->validateAccessToken($request);
        if (!$valid) {
            $data = array('message' => 'invalid_token');
            
            return new JsonResponse($data, JsonResponse::HTTP_UNAUTHORIZED);
        }
        
        $resource = $this->findResource($name);
        if (empty($resource)) {
            throw $this->createNotFoundException('Could not find image: ' . $name);
        }
        
        $this->loadFile($resource);
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
        
        exit;
    }
    
    protected function validateAccessToken(Request $request) {
        $tokenHeader = $request->headers->get('Authorization');
        if (empty($tokenHeader)) {
            return false;
        }
        
//        $tokenArr = split(' ', $tokenHeader);
//        $token = $tokenArr[1];
//        if (empty($token)) {
//            return false;
//        }
//        /* @var $buzz Browser */    
//        $buzz = $this->container->get('buzz');
//        $response = $buzz->submit(
//            $this->generateUrl('ms_oauth_access_token_validation', array('token' => $token))
//        );
//        $statusCode = $response->getHeader('Status Code');
//        
//        return $statusCode === JsonResponse::HTTP_OK;
        return true;
    }
}

