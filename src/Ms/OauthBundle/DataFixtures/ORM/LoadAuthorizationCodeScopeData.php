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
        $manager->persist($this->createScope(
            AuthorizationCodeScope::BASIC,
            'Το βασικό σύνολο πληροφοριών.'
        ));
        $manager->persist($this->createScope(
            AuthorizationCodeScope::FULL,
            'Ολόκληρο το σύνολο πληροφοριών.'
        ));
        $manager->flush();
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
