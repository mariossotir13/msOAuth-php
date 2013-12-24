<?php

namespace Ms\OauthBundle\Component\Authentication;

use Ms\OauthBundle\Entity\Client;
use Ms\OauthBundle\Component\Authentication\PasswordGeneratorInterface;
use Ms\OauthBundle\Component\Authentication\ClientIdGeneratorInterface;
use Ms\OauthBundle\Component\Authentication\CipherGeneratorInterace;

/**
 * Description of MsOauthAuthenticationService
 *
 * @author user
 */
class MsOauthAuthenticationService implements AuthenticationServiceInterface {

    /**
     *
     * @var CipherGeneratorInterace
     */
    protected $cipherGen;

    /**
     * @var ClientIdGeneratorInterface
     */
    protected $clientIdGen;

    /**
     * @var PasswordGeneratorInterface
     */
    protected $passGenerator;

    /**
     * 
     * @param ClientIdGeneratorInterface $clientIdGen
     * @param PasswordGeneratorInterface $passGen
     * @param CipherGeneratorInterace $cipherGen
     * @throws \InvalidArgumentException εάν οποιοδήποτε όρισμα είναι `null`.
     */
    function __construct(ClientIdGeneratorInterface $clientIdGen,
            PasswordGeneratorInterface $passGen,
            CipherGeneratorInterace $cipherGen) {
        if ($clientIdGen === null) {
            throw new \InvalidArgumentException('No client ID generator was provided.');
        }
        if ($passGen === null) {
            throw new \InvalidArgumentException('No password generator was provided.');
        }
        if ($cipherGen === null) {
            throw new \InvalidArgumentException('No cipher generator was provided.');
        }
        $this->clientIdGen = $clientIdGen;
        $this->passGenerator = $passGen;
        $this->cipherGen = $cipherGen;
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
    public function decryptPassword($password, $key) {
        return $this->cipherGen->decrypt($password, $key);
    }

    /**
     * @inheritdoc
     */
    public function encryptPassword($password, $key) {
        return $this->cipherGen->encrypt($password, $key);
    }

    /**
     * @inheritdoc
     */
    public function hashPassword($password, $salt) {
        return $this->passGenerator->hashPassword($password, $salt);
    }
}
