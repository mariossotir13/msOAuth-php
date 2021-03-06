<?php

namespace Ms\OauthBundle\Controller;

use Ms\OauthBundle\Component\Authorization\AuthorizationRequest;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Ms\OauthBundle\Entity\Client;
use Ms\OauthBundle\Entity\AuthorizationCodeScope;
use Ms\OauthBundle\Component\Authorization\AuthorizationErrorResponse;
use Ms\OauthBundle\Component\Authorization\AuthorizationError;
use Ms\OauthBundle\Component\Authorization\AuthorizationServiceInterface;
use Ms\OauthBundle\Entity\AuthorizationCodeProfile;
use Ms\OauthBundle\Component\Authorization\AuthorizationResponse;
use Ms\OauthBundle\Component\Authorization\ValidationResponse;
use Ms\OauthBundle\Component\Authorization\AccessTokenRequest;
use Ms\OauthBundle\Entity\AccessTokenProfile;
use Ms\OauthBundle\Component\Authorization\AccessTokenErrorResponse;
use Ms\OauthBundle\Component\Authorization\AccessTokenResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Ms\OauthBundle\Entity\ResourceOwner;

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
        $authResponse = new AuthorizationErrorResponse(AuthorizationError::ACCESS_DENIED);
        $authResponse->setErrorDescription('The owner denied access to her resources.');
        $authRequest = $this->createAuthorizationRequest($request);
        $authResponse->setState($authRequest->getState());
        $url = $authRequest->getRedirectionUri() . '?' . $authResponse->toQueryString();

        return $this->redirect($url);
    }

    /**
     * 
     * @param Request $request
     * @return Response
     */
    public function accessTokenAction(Request $request) {
        $accessTokenRequest = $this->createAccessTokenRequest($request);
        $validationResponse = $this->validateAccessTokenRequest($accessTokenRequest);
        if (!$validationResponse->isValid()) {
//            return $this->invalidAccessTokenRequestAction($validationResponse, $accessTokenRequest);
            return $this->invalidAccessTokenRequestAction($validationResponse);
        }

        $accessTokenProfile = $this->createAccessToken($accessTokenRequest);

        return new AccessTokenResponse(
            $this->container->getParameter('access_token_expires_in'),
            $accessTokenProfile->getScopes(),
            $accessTokenProfile->getAccessToken(),
            $accessTokenProfile->getAccessTokenType()
        );
    }

    /**
     * 
     * @param Request $request
     * @return Response
     * TODO: Να αλλαχθεί ο τρόπος ελέγχου αυθεντικοποίησης του ιδιοκτήτη πόρου μόλις ολοκληρωθεί
     *       η Π.Χ. *Αυθεντικοποίηση Ιδιοκτήτη Πόρου*.
     */
    public function authorizationCodeAction(Request $request) {
        $authRequest = $this->createAuthorizationRequest($request);
        $validationResponse = $this->validateAuthorizationRequest($authRequest);
        if (!$validationResponse->isValid()) {
            return $this->invalidAuthorizationRequestAction($validationResponse, $authRequest);
        }

        if (!$this->isAuthorizationRequestAccepted($request)) {
            return $this->redirect(
                $this->generateUrl(
                    'ms_oauth_authorization_acceptance', 
                    array(AuthorizationRequest::QUERY_PARAM => $authRequest->toQueryStringParameterValue())
                )
            );
        }

        $authCode = $this->createAuthorizationCode($authRequest);
        $authResponse = new AuthorizationResponse($authRequest->getRedirectionUri(), $authCode);
        $authResponse->setState($authRequest->getState());

        return $this->redirect($authResponse->toUri());
    }
    
    /**
     * 
     * @param Request $request
     * @return Response
     */
    public function invalidClientAction(Request $request) {
//        $accessTokenRequest = $this->createAccessTokenRequest($request);
        
        $violations = array(
            array(
                'message' => $request->query->get(AuthenticationController::AUTH_ERROR),
                'messageTemplate' => 'Invalid username or password.',
                'messageParameters' => array(),
                'root' => '',
                'propertyPath' => 'clientId',
                'invalidValue' => ''
            )
        );
        $validationResponse = ValidationResponse::fromArray($violations);
        
//        return $this->invalidAccessTokenRequestAction($validationResponse, $accessTokenRequest);
        return $this->invalidAccessTokenRequestAction($validationResponse);
    }

    /**
     * 
     * @param Request $request
     * @return Response
     */
    public function resourceOwnerAcceptanceAction(Request $request) {
        $authRequest = $this->createAuthorizationRequest($request);

        return $this->render(
            'MsOauthBundle:Authorization:request_acceptance_page.html.twig', 
            array(
                AuthorizationRequest::QUERY_PARAM => $authRequest->toQueryStringParameterValue(),
                'client' => $this->getClientFromRequest($authRequest),
                'scopes' => $this->getScopesFromRequest($authRequest)
            )
        );
    }
    
    /**
     * 
     * @param Request $request
     * @param string $token
     * @return JsonResponse
     */
    public function tokenInfoAction(Request $request, $token) {
        $repository = $this->getDoctrine()->getRepository('MsOauthBundle:AccessTokenProfile');
        /* @var $profile AccessTokenProfile */
        $profile = $repository->findOneByAccessToken($token);
        if ($profile === null) {
            return new JsonResponse(
                'Invalid token: ' . $token,
                JsonResponse::HTTP_NOT_FOUND
            );
        }
        
        $authorizationCodeProfile = $profile->getAuthorizationCodeProfile();
        
        return new JsonResponse(array(
            'client' => array(
                'id' => $authorizationCodeProfile->getClient()->getId(),
                'redirection_uri' => $authorizationCodeProfile->getClient()->getRedirectionUri()
            ),
            'resource_owner' => $authorizationCodeProfile->getResourceOwner()->getUsername(),
            'token' => $token
        ));
    }

    /**
     * 
     * @param Request $request
     * @param string $token
     * @return JsonResponse
     */
    public function validateTokenAction(Request $request, $token) {
        $repository = $this->getDoctrine()->getRepository('MsOauthBundle:AccessTokenProfile');
        /* @var $profile AccessTokenProfile */    
        $profile = $repository->findOneByAccessToken($token);     
        if ($profile ===null) {
            return new JsonResponse(
                'Invalid token: ' . $token,
                Response::HTTP_NOT_FOUND
            );
        }
        
        $expirationDate = $profile->getExpirationDate();
        $now = new \DateTime('now', new \DateTimeZone('UTC'));
        if ($expirationDate <= $now) {
            return new JsonResponse(
                'Expired Token: ' . $token,
                Response::HTTP_NOT_FOUND
            );
        }
        
        return new JsonResponse();
    }
    /**
     * 
     * @param AuthorizationRequest $authRequest
     * @return string Τον Κωδικό Εξουσιοδότησης ο οποίος δημιουργήθηκε για την
     * `$authRequest`.
     */
    protected function createAuthorizationCode(AuthorizationRequest $authRequest) {
        /* @var $authService AuthorizationServiceInterface */
        $authService = $this->get('ms_oauthbundle_authorization');
        $authCode = $authService->createAuthorizationCode();
        $authCodeProfile = $this->createAuthorizationCodeProfile($authCode, $authRequest);
        $em = $this->getDoctrine()->getManager();
        $em->persist($authCodeProfile);
        $em->flush();

        return $authCode;
    }

    /**
     * 
     * @param string $authorizationCode
     * @param AuthorizationRequest $authRequest
     * @return AuthorizationCodeProfile
     */
    protected function createAuthorizationCodeProfile($authorizationCode, AuthorizationRequest $authRequest) {
        $profile = new AuthorizationCodeProfile();
        
        $expiresIn = $this->container->getParameter('code_grant_expires_in');
        $now = new \DateTime('now', new \DateTimeZone("UTC"));
        $expirationDate = $now->add(new \DateInterval('PT' . $expiresIn . 'S'));

        $profile->setAuthorizationCode($authorizationCode)
            ->setClient($this->getClientFromRequest($authRequest))
            ->setExpirationDate($expirationDate)
            ->setRedirectionUri($authRequest->getRedirectionUri())
            ->setResourceOwner($this->getResourceOwnerAlreadyLoggedIn())
            ->setResponseType($authRequest->getResponseType())
            ->setState($authRequest->getState());

        $scopes = $this->getScopesFromRequest($authRequest);
        foreach ($scopes as $scope) {
            $profile->addScope($scope);
        }

        return $profile;
    }

    /**
     * 
     * @param AccessTokenRequest $request
     * @return AccessTokenProfile
     */
    protected function createAccessToken(AccessTokenRequest $request) {
        /* @var $authService AuthorizationServiceInterface */
        $authService = $this->get('ms_oauthbundle_authorization');
        $accessToken = $authService->createAccessToken();
        $profile = $this->createAccessTokenProfile($accessToken, $request);
        /* @var $em \Doctrine\Common\Persistence\AbstractManagerRegistry */
        $em = $this->getDoctrine()->getManager();
        $em->persist($profile);
        $em->flush();

        return $profile;
    }

    /**
     * 
     * @param string $accessToken
     * @param AuthorizationRequest $request
     * @return AccessTokenProfile
     */
    protected function createAccessTokenProfile($accessToken, AccessTokenRequest $request) {
        $profile = new AccessTokenProfile();
        
        $expiresIn = $this->container->getParameter('access_token_expires_in');
        $now = new \DateTime('now', new \DateTimeZone("UTC"));
        $expirationDate = $now->add(new \DateInterval('PT' . $expiresIn . 'S'));
        
        $authorizationCodeProfile = $this->findAuthorizationCodeProfile($request);

        $profile->setAccessToken($accessToken)
            ->setAuthorizationCodeProfile($authorizationCodeProfile)
            ->setAccessTokenType(AccessTokenProfile::ACCESS_TOKEN_TYPE_BEARER)
            ->setExpirationDate($expirationDate)
            ->setGrantType($request->getGrantType());

        $scopes = $authorizationCodeProfile->getScopes();
        foreach ($scopes as $scope) {
            $profile->addScope($scope);
        }

        return $profile;
    }

    /**
     * 
     * @param Request $request
     * @return AccessTokenRequest
     */
    protected function createAccessTokenRequest(Request $request) {
        $accessTokenRequest = AccessTokenRequest::fromRequest($request);
//        $accessTokenRequest->setClientRepository($this->getDoctrine()->getRepository("MsOauthBundle:Client"));

//        $requestParameter = $request->query->get(AccessTokenRequest::QUERY_PARAM);
//
//        $accessTokenRequest = ($requestParameter !== null) 
//            ? AccessTokenRequest::fromUri($requestParameter) 
//            : AccessTokenRequest::fromRequest($request);
        $accessTokenRequest->setAuthorizationCodeRepository(
            $this->getDoctrine()->getRepository("MsOauthBundle:AuthorizationCodeProfile")
        );
        $accessTokenRequest->setDoctrineRegistry($this->getDoctrine());

        return $accessTokenRequest;
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
    protected function createAuthorizationRequest(Request $request) {
        $requestParameter = $request->query->get(AuthorizationRequest::QUERY_PARAM);

        $authRequest = ($requestParameter !== null) 
            ? AuthorizationRequest::fromUri($requestParameter) 
            : AuthorizationRequest::fromRequest($request);
        $authRequest->setClientRepository($this->getDoctrine()->getRepository("MsOauthBundle:Client"));

        return $authRequest;
    }

    /**
     * 
     * @param AccessTokenRequest $request
     * @return AuthorizationCodeProfile
     */
    protected function findAuthorizationCodeProfile(AccessTokenRequest $request) {
        $clientId = $request->getClientId();
        $redirectionUri = $request->getRedirectionUri();
        $repository = $this->getDoctrine()->getRepository('MsOauthBundle:AuthorizationCodeProfile');

        return $repository->findOneByAuthorizationCode($request->getCode());
    }

    /**
     * 
     * @param AuthorizationRequest $authRequest
     * @return Client
     */
    protected function getClientFromRequest(AuthorizationRequest $authRequest) {
        $clientId = $authRequest->getClientId();
        $clientRepo = $this->getDoctrine()->getRepository('Ms\OauthBundle\Entity\Client');

        return $clientRepo->find($clientId);
    }
    
    /**
     * 
     * @return ResourceOwner
     */
    protected function getResourceOwnerAlreadyLoggedIn() {
        /* @var $securityContext \Symfony\Component\Security\Core\SecurityContextInterface */
        $securityContext = $this->get('security.context');
        
        return $securityContext->getToken()->getUser();
    }

    /**
     * 
     * @param AuthorizationRequest $authRequest
     * @return AuthorizationCodeScope[]
     */
    protected function getScopesFromRequest(AuthorizationRequest $authRequest) {
        $scopeRepo = $this->getDoctrine()->getRepository('Ms\OauthBundle\Entity\AuthorizationCodeScope');
        $scopesTitles = $authRequest->getScopes();
        $scopes = array();
        foreach ($scopesTitles as $title) {
            $scopes[] = $scopeRepo->findOneByTitle($title);
        }

        return $scopes;
    }

    /**
     * 
     * @param ValidationResponse $validationResponse
     * @param AuthorizationRequest $request
     * @return Response
     */
    protected function invalidAccessTokenRequestAction(ValidationResponse $validationResponse) {
//            ValidationResponse $validationResponse, 
//            AccessTokenRequest $request) {
//        $redirectionUri = $request->getRedirectionUri();
        $error = $validationResponse->getError();
        $errorMsg = $validationResponse->getErrorMessage();
        
//        $response = new AccessTokenErrorResponse($error, $redirectionUri);
        $response = new AccessTokenErrorResponse($error);
        $response->setErrorDescription($errorMsg);
        
        return $response->setUpAsJson();
    }

    /**
     * 
     * @param ValidationResponse $validationResponse
     * @param AuthorizationRequest $authRequest
     * @return Response
     */
    protected function invalidAuthorizationRequestAction(ValidationResponse $validationResponse, AuthorizationRequest $authRequest) {
        $response = new AuthorizationErrorResponse($validationResponse->getError());
        $response->setErrorDescription($validationResponse->getErrorMessage());
        $response->setState($authRequest->getState());

        if (!$response->isRedirected()) {
            return $this->render(
                'MsOauthBundle:Authorization:invalid_request_page.html.twig', 
                array('errorDescription' => $response->getErrorDescription())
            );
        }

        $url = $authRequest->getRedirectionUri() . '?' . $response->toQueryString();

        return $this->redirect($url);
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

    /**
     * 
     * @param AccessTokenRequest $authRequest
     * @return ValidationResponse
     */
    protected function validateAccessTokenRequest(AccessTokenRequest $authRequest) {
        /* @var $validator \Symfony\Component\Validator\Validator */
        $validator = $this->get('validator');
        /* @var $violationsList \Symfony\Component\Validator\ConstraintViolationListInterface */
        $violationsList = $validator->validate($authRequest);
        $propertyToErrorMap = array(
            'code' => AuthorizationError::INVALID_GRANT,
            'clientId' => AuthorizationError::INVALID_CLIENT,
            'scopes' => AuthorizationError::INVALID_SCOPE,
            'grantType' => AuthorizationError::UNSUPPORTED_GRANT_TYPE
        );

        return new ValidationResponse($violationsList, $propertyToErrorMap);
    }

    /**
     * 
     * @param AuthorizationRequest $authRequest
     * @return ValidationResponse
     */
    protected function validateAuthorizationRequest(AuthorizationRequest $authRequest) {
        /* @var $validator \Symfony\Component\Validator\Validator */
        $validator = $this->get('validator');
        /* @var $violationsList \Symfony\Component\Validator\ConstraintViolationListInterface */
        $violationsList = $validator->validate($authRequest);
        $propertyToErrorMap = array(
            'clientId' => AuthorizationError::INVALID_CLIENT,
            'redirectionUri' => AuthorizationError::REDIRECTION_URI,
            'responseType' => AuthorizationError::UNSUPPORTED_RESPONSE_TYPE,
            'scopes' => AuthorizationError::INVALID_SCOPE
        );

        return new ValidationResponse($violationsList, $propertyToErrorMap);
    }
}
