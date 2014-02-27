<?php

namespace Ms\OauthBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Ms\OauthBundle\Entity\AuthorizationCodeScope;

/**
 * Description of LoadAuthorizationCodeScopeData
 *
 * @author Marios
 */
class LoadAuthorizationCodeScopeData extends AbstractFixture implements OrderedFixtureInterface {

    /**@#+
     * 
     * @var string
     */
    const REF_BASIC = 'basic';
    const REF_FULL = 'full';
    /**@#-*/

    /**
     * @inheritdoc
     */
    public function getOrder() {
        return 2;
    }

    /**
     * @inheritdoc
     */
    public function load(ObjectManager $manager) {
        $basic = $this->createScope(
            AuthorizationCodeScope::BASIC,
            'Δικαίωμα προσπέλασης των πόρων σας μόνο για ανάγνωση.'
        );
        $full = $this->createScope(
            AuthorizationCodeScope::FULL,
            'Δικαίωμα επεξεργασίας των πόρων σας.'
        );
        
        $manager->persist($basic);
        $manager->persist($full);
        $manager->flush();
        
        $this->addReference(self::REF_BASIC, $basic);
        $this->addReference(self::REF_FULL, $full);
    }

    /**
     * 
     * @param string $title
     * @param string $description
     * @return AuthorizationCodeScope
     */
    private function createScope($title, $description) {
        $scope = new AuthorizationCodeScope();
        $scope->setTitle($title);
        $scope->setDescription($description);

        return $scope;
    }
}
