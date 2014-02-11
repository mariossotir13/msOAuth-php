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
    private static $EXPIRES_IN = 3600;

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
        $manager->persist( $this->createExpiredAuthorizationCodeProfile() );
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
        /* @var $resourceOwner \Ms\OauthBundle\Entity\ResourceOwner */
        $resourceOwner = $this->getReference(LoadUserData::REF_RESOURCE_OWNER);
        
        $expirationDate = new \DateTime('now', new \DateTimeZone('UTC'));
        $expirationDate->add(new \DateInterval('PT' . static::$EXPIRES_IN . 'S'));
        
        $profile = new AuthorizationCodeProfile();
        $profile->setAuthorizationCode('ECVkbAobtKSh9IN98WBcpAV4k3s6HXHh/bibF80MKus')
            ->setClient($client)
            ->setExpirationDate($expirationDate)
            ->setRedirectionUri($client->getRedirectionUri())
            ->setResourceOwner($resourceOwner)
            ->setResponseType('code')
            ->setState('RdoTKJnaUxdRfE7QBTZX')
            ->addScope( $this->getReference(LoadAuthorizationCodeScopeData::REF_BASIC) );
        
        return $profile;
    }
    
    /**
     * 
     * @return AuthorizationCodeProfile
     */
    private function createExpiredAuthorizationCodeProfile() {
        /* @var $client \Ms\OauthBundle\Entity\Client */
        $client = $this->getReference(LoadUserData::REF_CLIENT);
        /* @var $resourceOwner \Ms\OauthBundle\Entity\ResourceOwner */
        $resourceOwner = $this->getReference(LoadUserData::REF_RESOURCE_OWNER);
        
        $expirationDate = new \DateTime('now', new \DateTimeZone('UTC'));
        $expirationDate->add(new \DateInterval('PT1S'));
        
        $profile = new AuthorizationCodeProfile();
        $profile->setAuthorizationCode('2DJaB1A7VsbFmr1H3AkV/DLCR9s7rBPtHb5R/wqK9O4')
            ->setClient($client)
            ->setExpirationDate($expirationDate)
            ->setRedirectionUri($client->getRedirectionUri())
            ->setResourceOwner($resourceOwner)
            ->setResponseType('code')
            ->setState('RdoTKJnaUxdRfE7QBTZX')
            ->addScope( $this->getReference(LoadAuthorizationCodeScopeData::REF_BASIC) );
        
        return $profile;
    }
}
