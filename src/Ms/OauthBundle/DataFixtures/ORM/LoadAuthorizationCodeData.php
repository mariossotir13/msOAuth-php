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
     *
     * @var int
     */
    private static $EXPIRES_IN = 600;

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
        
        $expirationDate = new \DateTime('now', new \DateTimeZone('UTC'));
        $expirationDate->add(new \DateInterval('PT' . static::$EXPIRES_IN . 'S'));
        
        $profile = new AuthorizationCodeProfile();
        $profile->setAuthorizationCode('ECVkbAobtKSh9IN98WBcpAV4k3s6HXHh/bibF80MKus')
            ->setClient($client)
            ->setExpirationDate($expirationDate)
            ->setRedirectionUri($client->getRedirectionUri())
            ->setResponseType('code')
            ->setState('RdoTKJnaUxdRfE7QBTZX')
            ->addScope( $this->getReference(LoadAuthorizationCodeScopeData::REF_BASIC) );
        
        return $profile;
    }
}
