<?php

namespace Ms\OauthBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Ms\OauthBundle\Entity\Resource;

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
     * @param string $name
     * @return void
     */
    public function imageAction($name) {
        $resource = $this->findResource($name);
        if (empty($resource)) {
            return $this->createNotFoundException('Could not find image: ' . $name);
        }
        
        return $this->loadFile($resource);
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
        if ($fileInfo->isFile() && $fileInfo->isReadable()) {
            readfile($fileInfo->getRealPath());
        }
        
        exit;
    }
}

