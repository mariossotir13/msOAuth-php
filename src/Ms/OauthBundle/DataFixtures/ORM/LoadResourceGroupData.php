<?php

namespace Ms\OauthBundle\DataFixtures\ORM;

use Ms\OauthBundle\Entity\ResourceGroup;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Ms\OauthBundle\DataFixtures\ORM\LoadResourceData;

/**
 * Description of LoadResourceGroupData
 *
 * @author Marios
 */
class LoadResourceGroupData extends AbstractFixture implements OrderedFixtureInterface {
    
    /**
     * @inheritdoc
     */
    public function getOrder() {
        return 6;
    }

    /**
     * @inheritdoc
     */
    public function load(ObjectManager $manager) {
        $group = new ResourceGroup();
        $group->setTitle('Van Gogh Paintings')
            ->addResource($this->getReference(LoadResourceData::REF_RESOURCE_IMG_VANGOGH_1))
            ->addResource($this->getReference(LoadResourceData::REF_RESOURCE_IMG_VANGOGH_2))
            ->addResource($this->getReference(LoadResourceData::REF_RESOURCE_IMG_VANGOGH_3))
            ->addResource($this->getReference(LoadResourceData::REF_RESOURCE_IMG_VANGOGH_4))
            ->addResource($this->getReference(LoadResourceData::REF_RESOURCE_IMG_VANGOGH_5))
            ->setOwner($this->getReference(LoadUserData::REF_RESOURCE_OWNER));
        $manager->persist($group);
        
        $manager->flush();
    }
}
