<?php

namespace Ms\OauthBundle\Tests\Component\Authentication;

use Ms\OauthBundle\Component\Authentication\MsOauthClientIdGenerator;
use Ms\OauthBundle\Entity\Client;
use Ms\OauthBundle\Tests\Fixtures\Component\Authentication\ClientA;

/**
 * Δοκιμάζει τη λειτουργικότητα της κλάσης MsOauthClientIdGenerator.
 *
 * @package Ms\OauthBundle\Tests\Component\Authentication
 * @author Marios
 */
class MsOauthClientIdGeneratorTest extends \PHPUnit_Framework_TestCase {
    
    /**
     * Παρέχει δεδομένα στις δοκιμές της μεθόδου *generate*.
     * 
     * @return array Τα δεδομένα για κάθε ξεχωριστή εκτέλεση μίας δοκιμής.
     */
    public function provideToGenerateTest() {
        $data = array();
        
        $client = new ClientA();
        $data[] = array($client, $client->getId());

        return $data;
    }
    
    /**
     * Εξετάζει εάν τα Αναγνωριστικά Πελάτη τα οποία δημιουργούνται από τη μέθοδο
     * *generate* είναι σωστά.
     * 
     * Κατ' αρχάς, παρέχονται ένας Πελάτης και το Αναγνωριστικό Πελάτη το οποίο
     * αντιστοιχεί σε αυτόν. Το Αναγνωριστικό Πελάτη έχει προϋπολογιστεί και γνωρίζουμε
     * ότι είναι σωστό.
     * 
     * Έπειτα, προχωράμε στη δημιουργία ενός νέου Αναγνωριστικού Πελάτη χρησιμοποιώντας
     * ένα στιγμιότυπο της υπό δοκιμή κλάσης.
     * 
     * Εάν το παραχθέν αναγνωριστικό είναι το αναμενόμενο, τότε η κλάση παράγει
     * σωστά Αναγνωριστικά Πελάτη.
     * 
     * @param \Ms\OauthBundle\Entity\Client $client Ο Πελάτης του οποίου το Αναγνωριστικό
     * θα υπολογιστεί.
     * @param string $expectedId Το Αναγνωριστικό Πελάτη το οποίο αναμένεται να
     * δημιουργηθεί για τον Πελάτη `$client`.
     * @dataProvider provideToGenerateTest
     */
    public function testGenerate(Client $client, $expectedId) {
        $generator = new MsOauthClientIdGenerator();
        $id = $generator->generate($client);
        $this->assertEquals($expectedId, $id);
    }
}
