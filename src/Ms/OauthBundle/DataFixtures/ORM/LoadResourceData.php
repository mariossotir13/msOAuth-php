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
class LoadResourceData extends AbstractFixture implements OrderedFixtureInterface {
    
    /**#@+
     * 
     * @var string
     */
    const REF_RESOURCE_IMG_VANGOGH_1 = 'ref_resource_img_vangogh_1';
    const REF_RESOURCE_IMG_VANGOGH_2 = 'ref_resource_img_vangogh_2';
    const REF_RESOURCE_IMG_VANGOGH_3 = 'ref_resource_img_vangogh_3';
    const REF_RESOURCE_IMG_VANGOGH_4 = 'ref_resource_img_vangogh_4';
    const REF_RESOURCE_IMG_VANGOGH_5 = 'ref_resource_img_vangogh_5';
    /**#@-*/

    /**
     * Κάθε στοιχείο έχει την εξής δομή:
     * 
     *      array(
     *          'path' => {path to the image file},
     *          'reference' => {name of the entity reference},
     *          'title' => {image title}
     *      )
     * 
     * @var array
     */
    private static $IMAGES = array(
        array(
            'path' => 'images/a-cafe-night-on-place-lamartine-in-arles.jpg',
            'reference' => self::REF_RESOURCE_IMG_VANGOGH_1,
            'title' => 'A Cafe Night At Lamartine In Arles'
        ),
        array(
            'path' => 'images/a-pair-of-shoes.jpg',
            'reference' => self::REF_RESOURCE_IMG_VANGOGH_2,
            'title' => 'A Pair of Shoes'
        ),
        array(
            'path' => 'images/first-steps.jpg',
            'reference' => self::REF_RESOURCE_IMG_VANGOGH_3,
            'title' => 'First Steps'
        ),
        array(
            'path' => 'images/langlois-bridge-at-arles-with-women-washing.jpg',
            'reference' => self::REF_RESOURCE_IMG_VANGOGH_4,
            'title' => 'Langlois Bridge At Arles With Women Washing'
        ),
        array(
            'path' => 'images/starry-night.jpg',
            'reference' => self::REF_RESOURCE_IMG_VANGOGH_5,
            'title' => 'Starry Night'
        )
    );

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
        foreach (static::$IMAGES as $image) {
            $resource = $this->createImageResource($image['path'], $image['title']);
            $manager->persist($resource);
            
            $this->addReference($image['reference'], $resource);
        }
        
        $manager->persist( $this->createImageResource('images/img_1.jpg', '1') );
        
        $manager->flush();
    }

    /**
     * 
     * @param string $path
     * @param string $title
     * @return Resource
     */
    private function createImageResource($path, $title) {
        $resource = new Resource();
        $resource->setContent($path)
            ->setMimeType('image/jpg')
            ->setTitle($title);

        return $resource;
    }

}
