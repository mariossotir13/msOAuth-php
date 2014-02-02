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
    
    public function getOrder() {
        return 5;
    }

    public function load(ObjectManager $manager) {
        
        $resource = new Resource();
        
        $resource->setContent('Lorem ipsum dolor sit amet.')
                ->setMimeType('text/plain')
                ->setTitle('quote');
        
        $manager->persist($resource);
        
        $manager->flush();
    }    
}

