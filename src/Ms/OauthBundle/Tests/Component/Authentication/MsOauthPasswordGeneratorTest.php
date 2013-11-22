<?php

namespace Ms\OauthBundle\Tests\Component\Authentication;

use Ms\OauthBundle\Component\Authentication\MsOauthPasswordGenerator;

/**
 * Δοκιμάζει τη λειτουργικότητα της κλάσης MsOauthPasswordGenerator.
 *
 * @package Ms\OauthBundle\Tests\Component\Authentication
 * @author Marios
 */
class MsOauthPasswordGeneratorTest extends \PHPUnit_Framework_TestCase {
    
    /**
     * Το πλήθος των παραγόμενων αποτελεσμάτων σε κάθε δοκιμή.
     * 
     * @var int
     */
    private static $RESULTS_TOTAL = 300;
    
    /**
     * Ο υπό δοκιμή MsOauthPasswordGenerator.
     *
     * @var MsOauthPasswordGenerator
     */
    private $generator;
    
    /**
     * Εξετάζει τη μοναδικότητα των αποτελεσμάτων της μεθόδου *createSalt*.
     * 
     * Για μία λεπτομερή περιγραφή του αλγόριθμου της δοκιμής δείτε τη δοκιμή
     * *testCreatePassword*.
     */
    public function testCreateSalt() {
        $results = array();
        for ($i = 0; $i < static::$RESULTS_TOTAL; $i++) {
            $salt = $this->generator->createSalt();
            if (in_array($salt, $results)) {
                $this->fail("Duplicate salt: {$salt} (iteration: {$i}");
            }
            $results[] = $salt;
        }
        
        return $results;
    }
    
    /**
     * Εξετάζει τη μοναδικότητα των αποτελεσμάτων της μεθόδου *createPassword*.
     * 
     * Οι δοκιμές γίνονται χρησιμοποιώντας τα "αλάτια" τα οποία παράγονται κατά
     * τις δοκιμές της μεθόδου *testCreatePasswordSalt*.
     * 
     * Καθώς αναμένεται κάθε τιμή να είναι τυχαία οι κωδικοί δεν μπορούν να
     * ελεγχθούν με ένα προϋπολογισμένο σύνολο αποτελεσμάτων.
     * 
     * Η λύση την οποία ακολουθούμε είναι να παράγουμε ένα αρκετά μεγάλο πλήθος
     * αποτελεσμάτων και να τα αποθηκεύουμε καθώς παράγονται. Επιπλέον, μετά την
     * παραγωγή κάθε αποτελέσματος ελέγχουμε εάν αυτό έχει ήδη παραχθεί.
     * 
     * Η δοκιμή θεωρείται επιτυχής εάν δε βρεθεί διπλότυπο κανενός εκ των κωδικών
     * οι οποίοι έχουν παραχθεί.
     * 
     * Με τον όρο "αρκετά μεγάλο πλήθος" εννοούμε μία τιμή η οποία μας επιτρέπει
     * να εκτελούμε τις δοκιμές σε ένα εύλογο χρονικό διάστημα. Για παράδειγμα,
     * σε λιγότερο από δέκα δευτερόλεπτα.
     * 
     * @depends testCreatePasswordSalt
     * 
     * TODO: Ίσως πρέπει να αφαιρεθεί η εξάρτηση από τη μέθοδο testCreatePasswordSalt
     * και να βρούμε άλλη λύση. Για παράδειγμα, θα μπορούσαμε να χρησιμοποιούμε
     * το ίδιο "αλάτι" για όλους τους παραγόμενους κωδικούς στηριζόμενοι στο γεγονός
     * ότι εάν δεν αποτυγχάνει με το ίδιο "αλάτι" δεν υπάρχει περίπτωση να αποτύχει
     * με τυχαίο.
     */
    public function testCreatePassword(array $salts) {
        $results = array();
        for ($i = 0; $i < static::$RESULTS_TOTAL; $i++) {
            $password = $this->generator->createPassword($salts[$i]);
            if (in_array($password, $results)) {
                $this->fail("Duplicate password: {$password} (iteration: {$i}");
            }
            $results[] = $password;
        }
    }
    
    /**
     * @inheritdoc
     */
    protected function setUp() {
        $this->generator = new MsOauthPasswordGenerator();
    }
    
    /**
     * @inheritdoc
     */
    protected function tearDown() {
        $this->generator = null;
    }
}
