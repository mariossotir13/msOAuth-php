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
     * @var string
     */
    private static $QUERY_DELETE_USERS = 'DELETE FROM Ms\OauthBundle\Entity\Client';
    
    /**
     * Το μονοπάτι της Σελίδας Εγγραφής Πελάτη.
     *
     * @var string
     */
    private static $ROUTE_CLIENT_REGISTRATION = '/registration/client';

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
     *  1. Ο τίτλος του εγγράφου περιέχει τη φράση "Client Registration".
     *  2. Υπάρχει μέσα στο έγγραφο μία φόρμα της οποίας το πρώτο στοιχείο έχει
     *     *id* ms_oauthbundle_client.
     */
    public function testClientForFormRequest() {
        $crawler = $this->webClient->request('GET', static::$ROUTE_CLIENT_REGISTRATION);
        $this->assertCount(
            1,
            $crawler->filter('head > title:contains("Client Registration")')
        );
        $this->assertCount(
            1,
            $crawler->filter('form > #ms_oauthbundle_client')
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
        $client = new ClientA();
        $form->setValues(array(
            'ms_oauthbundle_client[appTitle]' => $client->getAppTitle(),
            'ms_oauthbundle_client[redirectionUri]' => $client->getRedirectionUri(),
            'ms_oauthbundle_client[clientType]' => $client->getClientType(),
            'ms_oauthbundle_client[email]' => $client->getEmail(),
        ));
        $this->webClient->submit($form);
        
//        $route = '/client/' . urlencode($client->getId());
//        $this->assertTrue($this->webClient->getResponse()->isRedirect($route));
        $this->assertTrue($this->webClient->getResponse()->isRedirect());
        
        /* @var $repository \Doctrine\ORM\EntityRepository */
        $repository = $this->webClient->getContainer()
            ->get('doctrine')
            ->getManager()
            ->getRepository('Ms\OauthBundle\Entity\Client');
        /* @var $savedClient \Ms\OauthBundle\Entity\Client */
        $savedClient = $repository->find($client->getId());
        $this->assertEquals($client->getId(), $savedClient->getId());
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
        $this->deleteAllClients();
        parent::tearDown();
        $this->webClient = null;
    }
    
    private function deleteAllClients() {
        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $this->webClient->getContainer()->get('doctrine')->getManager();
        /* @var $query \Doctrine\ORM\Query */
        $query = $em->createQuery(static::$QUERY_DELETE_USERS);
        $query->execute();
    }
}
