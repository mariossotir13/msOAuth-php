<?php

namespace Ms\OauthBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Ms\OauthBundle\Entity\AuthorizationCodeProfile;
use Ms\OauthBundle\DataFixtures\ORM\LoadUserData;
use Ms\OauthBundle\DataFixtures\ORM\LoadAuthorizationCodeScopeData;

/**
 * Description of LoadAuthorizationCodeData
 *
 * @author Marios
 */
class LoadAuthorizationCodeData extends AbstractFixture implements OrderedFixtureInterface {

    /**
     * @var string
     */
    const REF_AUTHZ_CODE = 'authorization_code_profile';

    /**
     * @inheritdoc
     */
    public function getOrder() {
        return 3;
    }
    
    /**
     * @inheritdoc
     */
    public function load(ObjectManager $manager) {
        $profile = $this->createAuthorizationCodeProfile();
        
        $manager->persist($profile);
        $manager->flush();
        
        $this->addReference(self::REF_AUTHZ_CODE, $profile);
    }
    
    /**
     * 
     * @return AuthorizationCodeProfile
     */
    private function createAuthorizationCodeProfile() {
        /* @var $client \Ms\OauthBundle\Entity\Client */
        $client = $this->getReference(LoadUserData::REF_CLIENT);
        
        $profile = new AuthorizationCodeProfile();
        $profile->setAuthorizationCode('ECVkbAobtKSh9IN98WBcpAV4k3s6HXHh/bibF80MKus')
            ->setClient($client)
            ->setRedirectionUri($client->getRedirectionUri())
            ->setResponseType('code')
            ->setState('RdoTKJnaUxdRfE7QBTZX')
            ->addScope( $this->getReference(LoadAuthorizationCodeScopeData::REF_BASIC) );
        
        return $profile;
    }
}
