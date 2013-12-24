<?php

namespace Ms\OauthBundle\Component\Authentication;

/**
 * Υλοποιεί τον αλγόριθμο [AES][1].
 * 
 * Η υλοποίηση βασίζεται στο εγχειρίδιο της συνάρτησης [mcrypt_encrypt][2].
 *
 * 
 * [1]: http://en.wikipedia.org/wiki/Advanced_Encryption_Standard
 *      "Advanced Encryption Standard στη Wikipedia"
 * [2]: http://www.php.net/manual/en/function.mcrypt-encrypt.php
 *      "PHP: mcrypt_encrypt - Manual"
 * 
 * @author Marios
 */
class MsOauthCipherGenerator implements CipherGeneratorInterace {
    
    /**
     * Ο τύπος λειτουργίας του αλγόριθμου κρυπτογράφησης.
     *
     * @var string
     */
    private static $CIPHER_MODE = MCRYPT_MODE_CBC;
    
    /**
     * Το όνομα του αλγόριθμου κρυπτογράφησης.
     *
     * @var string
     */
    private static $CIPHER_NAME = MCRYPT_RIJNDAEL_128;
    
    /**
     * Το μέγεθος του διανύσματος αρχικοποίησης (initialization vector).
     *
     * @var int
     */
    private $ivSize = 0;
    
    /**
     * Δημιουργεί ένα νέο στιγμιότυπο της κλάσης MsOauthCipherGenerator.
     */
    function __construct() {
        $this->ivSize = mcrypt_get_iv_size(static::$CIPHER_NAME, static::$CIPHER_MODE);
    }
    
    /**
     * @inheritdoc
     */
    public function decrypt($ciphertext, $key) {
        $key = substr(hash('sha512', $key, true), 0, $this->ivSize);
        $ciphertext = base64_decode($ciphertext);
        $iv = substr($ciphertext, 0, $this->ivSize);
        $ciphertext = substr($ciphertext, $this->ivSize);
        $plaintext = mcrypt_decrypt(
            static::$CIPHER_NAME, 
            $key, 
            $ciphertext, 
            static::$CIPHER_MODE, 
            $iv
        );
        
        return rtrim($plaintext, "\0");
    }

    /**
     * @inheritdoc
     */
    public function encrypt($plaintext, $key) {
        $key = substr(hash('sha512', $key, true), 0, $this->ivSize);
        $iv = mcrypt_create_iv($this->ivSize, MCRYPT_RAND);
        $ciphertext = mcrypt_encrypt(
            static::$CIPHER_NAME, 
            $key, 
            $plaintext, 
            static::$CIPHER_MODE, 
            $iv
        );
        $ciphertext = $iv . $ciphertext;
        
        return base64_encode($ciphertext);
    }
}