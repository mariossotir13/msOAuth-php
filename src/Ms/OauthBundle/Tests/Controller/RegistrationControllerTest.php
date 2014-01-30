<?php

namespace Ms\OauthBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Client as WebClient;
use Ms\OauthBundle\Tests\Fixtures\Component\Authentication\ClientA;

/**
 * Εξετάζει τη λειτουργικότητα της κλάσης RegistrationController.
 *
 * @package Ms\OauthBundle\Tests\Controller
 * @author Marios
 */
class RegistrationControllerTest extends WebTestCase {
    
    /**
     * 
     * @var string
     */
    private static $QUERY_DELETE_USER = 'DELETE FROM MsOauthBundle:Client c WHERE c.id = :id';
    
    /**
     * Το μονοπάτι της Σελίδας Εγγραφής Πελάτη.
     *
     * @var string
     */
    private static $ROUTE_CLIENT_REGISTRATION = '/oauth2/c/registration/';
    
    /**
     *
     * @var Client
     */
    private $client;

    /**
     * Ο WebClient ο οποίος χρησιμοποιείται για την πραγματοποίηση αιτήσεων προς
     * το web server.
     * 
     * @var WebClient
     */
    private $webClient;

    /**
     * Εξετάζει τη μέθοδο *clientAction* όταν ο χρήστης ζητάει τη Σελίδα Εγγραφής
     * Πελάτη.
     * 
     * Πραγματοποιούνται δύο έλεγχοι:
     * 
     *  1. Ο τίτλος του εγγράφου περιέχει τη φράση "Εγγραφή Πελάτη".
     *  2. Υπάρχει μέσα στο έγγραφο μία φόρμα της οποίας το πρώτο στοιχείο έχει
     *     *id* ms_oauthbundle_client.
     */
    public function testClientForFormRequest() {
        $crawler = $this->webClient->request('GET', static::$ROUTE_CLIENT_REGISTRATION);
        $this->assertCount(
            1,
            $crawler->filter('head > title:contains("Εγγραφή Πελάτη")')
        );
        $this->assertCount(
            1,
            $crawler->filter('form[name="ms_oauthbundle_client"]')
        );
    }
    
    /**
     * Εξετάζει τη μέθοδο *clientAction* όταν ο χρήστης καταχωρεί τη Φόρμα Εγγραφής.
     * 
     * Η μέθοδος αυτή ελέγχει την περίπτωση όπου η Φόρμα Εγγραφής έχει συμπληρωθεί
     * σωστά. Σε μία τέτοια περίπτωση δημιουργείται ένας νέος Πελάτης και αποθηκεύεται
     * στη βάση δεδομένων, πράγμα το οποίο και ελέγχει αυτή η μέθοδος.
     * 
     * > Για έλεγχο της περίπτωσης καταχώρησης της φόρμας με μη έγκυρα στοιχεία
     * > δείτε τη δοκιμή *testClientForInvalidFormSubmission*.
     */
    public function testClientForFormSubmission() {
        $crawler = $this->webClient->request('GET', static::$ROUTE_CLIENT_REGISTRATION);
        $form = $crawler->selectButton('ms_oauthbundle_client[Submit]')->form();
        $this->client = new ClientA();
        $form->setValues(array(
            'ms_oauthbundle_client[appTitle]' => $this->client->getAppTitle(),
            'ms_oauthbundle_client[redirectionUri]' => $this->client->getRedirectionUri(),
            'ms_oauthbundle_client[clientType]' => $this->client->getClientType(),
            'ms_oauthbundle_client[email]' => $this->client->getEmail(),
        ));
        $this->webClient->submit($form);
        
        /* @var $repository \Doctrine\ORM\EntityRepository */
        $repository = $this->webClient->getContainer()
            ->get('doctrine')
            ->getManager()
            ->getRepository('Ms\OauthBundle\Entity\Client');
        /* @var $savedClient \Ms\OauthBundle\Entity\Client */
        $savedClient = $repository->find($this->client->getId());
        $this->assertNotNull($savedClient);
    }
    
    /**
     * Δημιουργεί το WebClient ο οποίος χρησιμοποιείται σε όλες τις δοκιμές.
     * 
     * @inheritdoc
     */
    protected function setUp() {
        parent::setUp();
        $this->webClient = static::createClient();
    }
    
    /**
     * @inhderitdoc
     */
    protected function tearDown() {
        $this->deleteClient();
        parent::tearDown();
        $this->webClient = null;
    }
    
    /**
     * @return void
     */
    private function deleteClient() {
        if ($this->client === null) {
            return;
        }
        
        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $this->webClient->getContainer()->get('doctrine')->getManager();
        /* @var $query \Doctrine\ORM\Query */
        $query = $em->createQuery(static::$QUERY_DELETE_USER)
            ->setParameter('id', $this->client->getId());
        $query->execute();
    }
}
