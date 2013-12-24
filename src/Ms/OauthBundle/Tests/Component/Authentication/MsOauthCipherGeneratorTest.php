<?php

namespace Ms\OauthBundle\Tests\Component\Authentication;

use Ms\OauthBundle\Component\Authentication\MsOauthCipherGenerator;

/**
 * Description of MsOauthCipherGeneratorTest
 *
 * @author Marios
 */
class MsOauthCipherGeneratorTest extends \PHPUnit_Framework_TestCase {

    /**
     *
     * @var string
     */
    private static $KEY = 'abcdef0123456789';

    /**
     *
     * @var string
     */
    private static $PLAINTEXT = 'Hello, world!';

    /**
     *
     * @var MsOauthCipherGenerator
     */
    private $generator;
    
    /**
     * 
     */
    public function testDecrypt() {
        $ciphertext = $this->generator->encrypt(static::$PLAINTEXT, static::$KEY);
        $plaintext = $this->generator->decrypt($ciphertext, static::$KEY);
        $this->assertEquals(static::$PLAINTEXT, $plaintext);
    }
    
    /**
     * 
     */
    public function testEncrypt() {
        $ciphertext = $this->generator->encrypt(static::$PLAINTEXT, static::$KEY);
        $this->assertNotEquals(static::$PLAINTEXT, $ciphertext);
        $this->assertNotEquals(static::$KEY, $ciphertext);
        
        $base64Charset = 'A-Za-z0-9\+/=';
        $this->assertEquals(
            1,
            preg_match('#[' . $base64Charset . ']+#', $ciphertext)
        );
        $this->assertEquals(
            0,
            preg_match('#[^' . $base64Charset . ']+#', $ciphertext)
        );
    }
    
    /**
     * @inheritdoc
     */
    protected function setUp() {
        $this->generator = new MsOauthCipherGenerator();
    }

    /**
     * @inheritdoc
     */
    protected function tearDown() {
        $this->generator = null;
    }
}
