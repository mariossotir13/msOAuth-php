<?php

namespace Ms\OauthBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use \Doctrine\Common\Persistence\ObjectManager;
use Ms\OauthBundle\Entity\Client;
use Ms\OauthBundle\Entity\User;

/**
 * Description of LoadUserData
 *
 * @author Marios
 */
class LoadUserData implements FixtureInterface {

    /**
     * @inheritdoc
     */
    public function load(ObjectManager $manager) {
        $client = $this->createClient();
        $manager->persist($client);
        
        $user = $this->createResourceOwner();
        $manager->persist($user);
        
        $manager->flush();
    }
    
    /**
     * 
     * @return Client
     */
    private function createClient() {
        $client = new Client();
        $client->setAppTitle('Demo 1')
            ->setRedirectionUri('http://msoauthphp.local/app_dev.php/client-app/demo1')
            ->setId('zMuobKhbnvJUTYc+EnXfRwiiHP4/OpmM5CLrdpkIsm4')
            ->setEmail('demo1@client.com')
            ->setPassword('jLCLq/YjRtQK9S1yMDBqS8i0fryF9YkUZJQVh1zlZrsrBpgE6orCQa2bNTlwJiIr');
        
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
