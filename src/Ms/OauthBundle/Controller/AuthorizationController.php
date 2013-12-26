<?php

namespace Ms\OauthBundle\Controller;

use Ms\OauthBundle\Component\Authorization\AuthorizationRequest;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Ms\OauthBundle\Controller\AuthenticationController;
use Ms\OauthBundle\Entity\Client;
use Ms\OauthBundle\Entity\AuthorizationCodeScope;
use Ms\OauthBundle\Component\Authorization\AuthorizationErrorResponse;
use Ms\OauthBundle\Component\Authorization\AuthorizationError;
use Ms\OauthBundle\Component\Authorization\AuthorizationServiceInterface;
use Ms\OauthBundle\Entity\AuthorizationCodeProfile;

/**
 * Description of AuthorizationController
 *
 * @author Marios
 */
class AuthorizationController extends Controller {
    
    /**
     * @var string
     */
    const PARAM_AUTHORIZATION_REQUEST_ACCEPT = 'authz_rq_accept';
    
    /**
     * 
     * @param Request $request
     * @return Response
     */
    public function accessDeniedAction(Request $request) {
        $authResponse = new AuthorizationErrorResponse();
        $authResponse->setError(AuthorizationError::ACCESS_DENIED);
        $authResponse->setErrorDescription('The owner denied access to her resources.');
        
        $authRequest = $this->createAuthorizationRequestFrom($request);
        $authResponse->setState($authRequest->getState());
        
        $redirectionUri = $authRequest->getRedirectionUri();
        $url = $redirectionUri . '?' . $authResponse->toUri();
        
        return $this->redirect($url);
    }

    /**
     * 
     * @param Request $request
     * @return Response
     * TODO: Να αλλαχθεί ο τρόπος ελέγχου αυθεντικοποίησης του ιδιοκτήτη πόρου μόλις ολοκληρωθεί η Π.Χ. *Αυθεντικοποίηση Ιδιοκτήτη Πόρου*.
     */
    public function authorizationCodeAction(Request $request) {
        $authRequest = $this->createAuthorizationRequestFrom($request);
        if (!AuthenticationController::isUserAuthenticated($request)) {
            return $this->redirect($this->generateUrl(
                'ms_oauth_authentication_resource_owner', 
                array(AuthorizationRequest::QUERY_PARAM => $authRequest->toUri())
            ));
        }
        
        if (!$this->isAuthorizationRequestAccepted($request)) {
            return $this->redirect($this->generateUrl(
                'ms_oauth_authorization_acceptance', 
                array(AuthorizationRequest::QUERY_PARAM => $authRequest->toUri())
            ));
        }
        
        /* @var $authService AuthorizationServiceInterface */
        $authService = $this->get('ms_oauthbundle_authorization');
        $authCode = $authService->createAuthorizationCode();
        $authCodeProfile = $this->createAuthorizationCodeProfileFrom($authCode, $authRequest);
        $em = $this->getDoctrine()->getManager();
        $em->persist($authCodeProfile);
        $em->flush();
        
        return new Response('Code: ' . $authCode);
    }
    
    /**
     * 
     * @param Request $request
     * @return Response
     */
    public function resourceOwnerAcceptanceAction(Request $request) {
        $authRequest = $this->createAuthorizationRequestFrom($request);
        
        return $this->render(
            'MsOauthBundle:Authorization:request_acceptance_page.html.twig',
            array(
                AuthorizationRequest::QUERY_PARAM => $authRequest->toUri(),
                'client' => $this->getClientFromRequest($authRequest),
                'scopes' => $this->getScopesFromRequest($authRequest)
            )
        );
    }
    
    /**
     * 
     * @param string $authorizationCode
     * @param AuthorizationRequest $request
     * @return AuthorizationCodeProfile
     */
    protected function createAuthorizationCodeProfileFrom($authorizationCode,
            AuthorizationRequest $request) {
        $profile = new AuthorizationCodeProfile();
        
        $profile->setAuthorizationCode($authorizationCode);
        $profile->setClient($this->getClientFromRequest($request));
        $profile->setRedirectionUri($request->getRedirectionUri());
        $profile->setResponseType($request->getResponseType());
        $profile->setState($request->getState());
        
        $scopes = $this->getScopesFromRequest($request);
        foreach ($scopes as $scope) {
            $profile->addScope($scope);
        }
        
        return $profile;
    }
    
    /**
     * Δημιουργεί ένα νέο στιγμιότυπο της κλάσης AuthorizationRequest χρησιμοποιώντας
     * πληροφορίες από ένα Request αντικείμενο.
     * 
     * Οι πληροφορίες περιέχονται:
     *  
     *  1. είτε ως ξεχωριστές παράμετροι στο [Query String][1]
     *  2. είτε ως μία παράμετρος με όλες τις πληροφορίες της αρχικής Αίτησης Εξουσιοδότησης.
     * 
     * Παράδειγμα της περίπτωσης (1) είναι το εξής URI:
     *      
     *      response_type=code&client_id=%2Fe51QfFyBAqooGsSZh7edFv0A%2FH8KiHrCe2PPderH0g
     *      &redirect_uri=http%3A%2F%2Fmsoauthphp.local%2Fapp_dev.php%2Fclient-app%2Fdemo1
     *      &scope=basic&state=v3l5iRmQ5ZdG7LJm9Cf3
     * 
     * Σημειώστε ότι το URI έχει διαιρεθεί σε 3 γραμμές για λόγους αναγνωσιμότητας.
     * Υπό κανονικές συνθήκες δεν υπάρχουν χαρακτήρες αλλαγής γραμμής στο URI.
     * 
     * Το ίδιο URI φέρνοτάς το στη μορφή της περίπτωσης (2) θα είναι:
     * 
     *      authz_rq=%26client_id%3D%2Fe51QfFyBAqooGsSZh7edFv0A%2FH8KiHrCe2PPderH0g
     *      %26redirect_uri%3Dhttp%3A%2F%2Fmsoauthphp.local%2Fapp_dev.php%2Fclient-app%2Fdemo1
     *      %26response_type%3Dcode%26state%3Dv3l5iRmQ5ZdG7LJm9Cf3%26scope%3Dbasic
     * 
     * Παρατηρήστε ότι όλες οι παράμετροι (π.χ., client_id) έχουν ενσωματωθεί σε
     * μία, συγκεκριμένα την παράμετρο *authz_rq*. Εξαιτίας αυτού βλέπουμε πως
     * έχουν κωδικοποιηθεί και κάποιοι χαρακτήρες οι οποίοι δεν είχαν κωδικοποιηθεί
     * στην περίπτωση (1). Για παράδειγμα, ο χαρακτήρας *&* (U+0026) έχει μετατραπεί
     * στην ακολουθία *%26*. Αυτό συνέβη γιατί, πλέον, αυτοί οι χαρακτήρες αποτελούν
     * μέρος της τιμής μίας παραμέτρου και, ως εκ τούτου, δεν έχουν ιδιαίτερη
     * σημασία για το URI.
     * 
     * Επιπλέον, αξίζει να σημειωθεί ότι η παράμετρος *authz_rq* έχει το όνομα
     * το οποίο καταδεικνύει η μεταβλητή AuthorizationRequest::QUERY_PARAM.
     * 
     * ### Αιτιολόγηση της Διπλής Αναπαράστασης ###
     * 
     * Κάποιος μπορεί να αναρωτηθεί γιατί να υπάρχουν δύο ξεχωριστές αναπαραστάσεις
     * της Αίτησης Εξουσιοδότησης.
     * 
     * Η ανάγκη για την περίπτωση (1) είναι η περισσότερο κατανοητή. Όλες οι παράμετροι
     * της Αίτησης Εξουσιοδότησης αποτελούν μεταβλητές οι οποίες παρέχονται στον
     * Εξυπηρετητή Εξουσιοδοτήσεων. Επομένως, είναι λογικό να αποσταλλούν ως
     * ξεχωριστές παράμετροι μέσω του Query String. Αυτήν την περίπτωση καλύπτει
     * η μέθοδος `authorizationCodeAction` όταν κληθεί αρχικά από έναν Πελάτη.
     * Δηλαδή, όταν ο Πελάτης δημιουργεί την Αίτηση Εξουσιοδότησης την αποστέλλει
     * σχηματίζοντας ζεύγη κλειδιών-τιμών για κάθε στοιχείο της Αίτησης Εξουσιοδότησης
     * ξεχωριστά.
     * 
     * Ωστόσο, οι μέθοδοι του παρόντος Controller καλούνται να αποστείλουν την Αίτηση
     * Εξουσιοδότησης στα πλαίσια των διαφόρων ανακατευθύνσεων. Για παράδειγμα,
     * αμέσως μετά τη λήψη της Αίτησης Εξουσιοδότησης η μέθοδος `authorizationCodeAction`
     * ανακατευθύνει τον Ιδιοκτήτη Πόρου στη Σελίδα Αποδοχής Αίτησης Εξουσιοδότησης.
     * Το URI το οποίο χρησιμοποιείται σε αυτήν την ανακατεύθυνση έχει τη μορφή
     * της περίπτωσης (2).
     * 
     * Αυτό συμβαίνει γιατί σε αυτό το URI ίσως προστεθούν και πληροφορίες οι οποίες
     * δεν αφορούν την Αίτηση Εξουσιοδότησης. Θέτοντας όλες τις πληροφορίες της
     * Αίτησης Εξουσιοδότησης ως τιμή της μεταβλητής *authz_rq* είμαστε σε θέση
     * να γνωρίζουμε άμεσα τις πληροφορίες της αρχικής αίτησης.
     * 
     * 
     * [1]: http://en.wikipedia.org/wiki/Query_string
     *      "Query String στη Wikipedia"
     * 
     * @param Request $request
     * @return AuthorizationRequest
     */
    protected function createAuthorizationRequestFrom(Request $request) {
        $requestParameter = $request->query->get(AuthorizationRequest::QUERY_PARAM);
        if ($requestParameter !== null) {
            return AuthorizationRequest::fromUri($requestParameter);
        }
        
        return AuthorizationRequest::fromRequest($request);
    }
    
    /**
     * 
     * @param AuthorizationRequest $request
     * @return Client
     */
    protected function getClientFromRequest(AuthorizationRequest $request) {
        $clientId = $request->getClientId();
        $clientRepo = $this->getDoctrine()->getRepository('Ms\OauthBundle\Entity\Client');

        return $clientRepo->find($clientId);
    }
    
    /**
     * 
     * @param AuthorizationRequest $request
     * @return AuthorizationCodeScope
     */
    protected function getScopesFromRequest(AuthorizationRequest $request) {
        $scopeRepo = $this->getDoctrine()->getRepository('Ms\OauthBundle\Entity\AuthorizationCodeScope');
        $scopesTitles = $request->getScopes();
        $scopes = array();
        foreach ($scopesTitles as $title) {
            $scopes[] = $scopeRepo->findOneByTitle($title);
        }
        
        return $scopes;
    }
    
    /**
     * 
     * @param Request $request
     * @return boolean
     */
    protected function isAuthorizationRequestAccepted(Request $request) {
        $accepted = (int) $request->query->get(static::PARAM_AUTHORIZATION_REQUEST_ACCEPT, 0);
        
        return $accepted === 1;
    }
}
