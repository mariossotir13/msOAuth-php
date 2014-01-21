<?php

namespace Ms\OauthBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Ms\OauthBundle\Entity\AccessTokenProfile;

/**
 * Description of LoadAccessTokenData
 *
 * @author Marios
 */
class LoadAccessTokenData extends AbstractFixture implements OrderedFixtureInterface {
    
    /**
     * @var string
     */
    const REF_TOKEN = 'access_token_profile';
    
    /**
     * @inheritdoc
     */
    public function getOrder() {
        return 4;
    }

    /**
     * @inheritdoc
     */
    public function load(ObjectManager $manager) {
        $profile = $this->createAccessTokenProfile();
        
        $manager->persist($profile);
        $manager->flush();
        
        $this->addReference(self::REF_TOKEN, $profile);
    }
    
    /**
     * 
     * @return AccessTokenProfile
     */
    private function createAccessTokenProfile() {
        /* @var $authzCode \Ms\OauthBundle\Entity\AuthorizationCodeProfile */
        $authzCode = $this->getReference(LoadAuthorizationCodeData::REF_AUTHZ_CODE);
        
        $profile = new AccessTokenProfile();
        $profile->setAccessToken('1wRAhqWY+WWy8RhlfIOjP9JCTy3ibrWMhaJ6DzjD9BU')
            ->setAccessTokenType('bearer')
            ->setAuthorizationCodeProfile($authzCode)
            ->setGrantType('authorization_code');
        
        foreach ($authzCode->getScopes() as $scope) {
            $profile->addScope($scope);
        }
        
        return $profile;
    }
}
