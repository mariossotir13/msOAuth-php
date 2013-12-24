<?php

namespace Ms\OauthBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Ms\OauthBundle\Entity\AuthorizationCodeScope;

/**
 * Description of LoadAuthorizationCodeScopeData
 *
 * @author Marios
 */
class LoadAuthorizationCodeScopeData implements FixtureInterface {

    /**
     * @inheritdoc
     */
    public function load(ObjectManager $manager) {
        $manager->persist( $this->createScope(AuthorizationCodeScope::BASIC) );
        $manager->persist( $this->createScope(AuthorizationCodeScope::FULL) );
        $manager->flush();
    }
    
    /**
     * 
     * @param string $title
     * @return AuthorizationCodeScope
     */
    private function createScope($title) {
        $scope = new AuthorizationCodeScope();
        $scope->setTitle($title);
        
        return $scope;
    }
}
