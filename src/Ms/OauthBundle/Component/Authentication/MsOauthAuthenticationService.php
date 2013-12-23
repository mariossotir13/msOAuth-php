<?php

namespace Ms\OauthBundle\Component\Authentication;

use Ms\OauthBundle\Entity\Client;
use Ms\OauthBundle\Component\Authentication\PasswordGeneratorInterface;
use Ms\OauthBundle\Component\Authentication\ClientIdGeneratorInterface;

/**
 * Description of MsOauthAuthenticationService
 *
 * @author user
 */
class MsOauthAuthenticationService implements AuthenticationServiceInterface {

    /**
     * @var ClientIdGeneratorInterface
     */
    private $clientIdGen;

    /**
     * @var PasswordGeneratorInterface
     */
    private $passGenerator;

    /**
     * 
     * @param ClientIdGeneratorInterface $clientIdGen
     * @param PasswordGeneratorInterface $passGen
     * @throws \InvalidArgumentException if `$passGen` is null.
     */
    function __construct(ClientIdGeneratorInterface $clientIdGen,
            PasswordGeneratorInterface $passGen) {
        if ($clientIdGen === null) {
            throw new \InvalidArgumentException('No client ID generator was provided.');
        }
        if ($passGen === null) {
            throw new \InvalidArgumentException('No password generator was provided.');
        }
        $this->clientIdGen = $clientIdGen;
        $this->passGenerator = $passGen;
    }

    /**
     * @inheritdoc
     */
    public function createClientId(Client $client) {
        return $this->clientIdGen->generate($client);
    }

    /**
     * @inheritdoc
     */
    public function createPassword() {
        return $this->passGenerator->createPassword();
    }

    /**
     * @inhderitdoc
     */
    public function createPasswordSalt() {
        return $this->passGenerator->createSalt();
    }

    /**
     * @inheritdoc
     */
    public function hashPassword($password, $salt) {
        return $this->passGenerator->hashPassword($password, $salt);
    }
}
