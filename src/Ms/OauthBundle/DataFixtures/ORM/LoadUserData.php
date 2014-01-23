<?php

namespace Ms\OauthBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use \Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Ms\OauthBundle\Entity\Client;
use Ms\OauthBundle\Entity\User;
use Ms\OauthBundle\Entity\ClientType;

/**
 * Description of LoadUserData
 *
 * @author Marios
 */
class LoadUserData extends AbstractFixture implements OrderedFixtureInterface {

    /**
     * @var string
     */
    const REF_CLIENT = 'client';

    /**
     * @inheritdoc
     */
    public function getOrder() {
        return 1;
    }
    
    /**
     * @inheritdoc
     */
    public function load(ObjectManager $manager) {
        $client = $this->createClient();
        $manager->persist($client);
        
        $user = $this->createResourceOwner();
        $manager->persist($user);
        
        $manager->flush();
        
        $this->addReference(self::REF_CLIENT, $client);
    }
    
    /**
     * 
     * @return Client
     */
    private function createClient() {
        $client = new Client();
        $client->setAppTitle('Demo 1')
            ->setClientType(ClientType::TYPE_CONFIDENTIAL)
            ->setRedirectionUri('http://msoauthphp.local/app_dev.php/client-app/demo1')
            ->setId('zMuobKhbnvJUTYc+EnXfRwiiHP4/OpmM5CLrdpkIsm4')
            ->setEmail('demo1@client.com')
            ->setPassword('T+QI3ETKu6hC7v3IsVr0A1YPRoyT7bH9eafG8Brh6neTNkuANR2VLfLw96J8MXUO');
        
        return $client;
    }
    
    /**
     * 
     * @return User
     */
    private function createResourceOwner() {
        $resourceOwner = new User();
        $resourceOwner->setId('Resource Owner Demo 1')
            ->setEmail('demo1@resourceowner.com')
            ->setPassword('/3U/51Yj3OLc5QjU3qBMjK8XIYWeBwsc5ftCSwDkWX/DaRiUAyvRYdN6a8ALwjfwYyUvmfjfhsfGkOneZe62WA==')
            ->setSalt('TzqIyVkrVRTIYQ==');
        
        return $resourceOwner;
    }
}
