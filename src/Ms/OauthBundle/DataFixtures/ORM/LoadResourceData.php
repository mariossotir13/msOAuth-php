<?php

namespace Ms\OauthBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use \Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Ms\OauthBundle\Entity\Resource;

/**
 * Description of LoadResourceData
 *
 * @author Marios
 */
class LoadResourceData extends AbstractFixture implements OrderedFixtureInterface{
    
    /**
     * @inheritdoc
     */
    public function getOrder() {
        return 5;
    }

    /**
     * @inheritdoc
     */
    public function load(ObjectManager $manager) {
        $manager->persist($this->createImageResource());
        $manager->persist($this->createTextResource());
        
        $manager->flush();
    }
    
    /**
     * 
     * @return Resource
     */
    private function createImageResource() {
        $resource = new Resource();
        $resource->setContent('images/img_1.jpg')
                ->setMimeType('image/jpg')
                ->setTitle('1');
        
        return $resource;
    }
    
    /**
     * 
     * @return Resource
     */
    private function createTextResource() {
        $resource = new Resource();
        $resource->setContent('Lorem ipsum dolor sit amet.')
                ->setMimeType('text/plain')
                ->setTitle('quote');
        
        return $resource;
    }
}

