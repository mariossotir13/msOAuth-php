<?php

namespace Ms\OauthBundle\Tests\Component\Authentication;

use Ms\OauthBundle\Component\Authentication\MsOauthAuthenticationService;
use Ms\OauthBundle\Tests\Fixtures\Component\Authentication\ClientA;

/**
 * Δοκιμάζει τη λειτουργικότητα της κλάσης MsOauthAuthenticationService.
 *
 * @package Ms\OauthBundle\Tests\Component\Authentication
 * @author Marios
 */
class MsOauthAuthenticationServiceTest extends \PHPUnit_Framework_TestCase {

    /**
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $clientIdGenerator;

    /**
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $passwordGenerator;

    /**
     * Η υπηρεσία υπό δοκιμή.
     *
     * @var MsOauthAuthenticationService
     */
    private $service;

    /**
     * Εξετάζει τη μέθοδο *createClientId*.
     * 
     * Η μέθοδος αυτή αναμένεται να καλέσει μία φορά τη μέθοδο *generate* ενός
     * *ClientIdGeneratorInterface*
     */
    public function testCreateClientId() {
        $client = new ClientA();
        $expectedId = '1';
        $this->clientIdGenerator->expects($this->once())
            ->method('generate')
            ->with($this->equalTo($client))
            ->will($this->returnValue($expectedId));
        $id = $this->service->createClientId($client);
        $this->assertEquals($expectedId, $id);
    }

    /**
     * Εξετάζει τη μέθοδο *createPassword*.
     * 
     * Η μέθοδος αυτή αναμένεται να καλέσει μία φορά τη μέθοδο *createPassword*
     * ενός *PasswordGeneratorInterface*.
     */
    public function testCreatePassword() {
        $expectedPassword = '1';
        $salt = '1';
        $this->passwordGenerator->expects($this->once())
            ->method('createPassword')
            ->with($this->equalTo($salt))
            ->will($this->returnValue($expectedPassword));
        $password = $this->service->createPassword($salt);
        $this->assertEquals($expectedPassword, $password);
    }

    /**
     * Εξετάζει τη μέθοδο *createPassword*.
     * 
     * Η μέθοδος αυτή αναμένεται να καλέσει μία φορά τη μέθοδο *createSalt* ενός
     * *PasswordGeneratorInterface*.
     */
    public function testCreatePasswordSalt() {
        $expectedSalt = '1';
        $this->passwordGenerator->expects($this->once())
            ->method('createSalt')
            ->will($this->returnValue($expectedSalt));
        $salt = $this->service->createPasswordSalt();
        $this->assertEquals($expectedSalt, $salt);
    }

    /**
     * Δημιουργεί ένα στιγμιότυπο της κλάσης MsOauthAuthenticationService.
     * 
     * @inheritdoc
     */
    protected function setUp() {
        $this->clientIdGenerator = $this->getMock('Ms\OauthBundle\Component\Authentication\MsOauthClientIdGenerator');
        $this->passwordGenerator = $this->getMock('Ms\OauthBundle\Component\Authentication\MsOauthPasswordGenerator');
        $this->service = new MsOauthAuthenticationService(
            $this->clientIdGenerator, 
            $this->passwordGenerator
        );
    }

    /**
     * @inheritdoc
     */
    protected function tearDown() {
        $this->clientIdGenerator = null;
        $this->passwordGenerator = null;
        $this->service = null;
    }

}
