<?php

namespace Ms\OauthBundle\Component\Authentication;

use Ms\OauthBundle\Entity\Client;

/**
 * Description of MsOauthAuthenticationService
 *
 * @author user
 */
class MsOauthAuthenticationService implements AuthenticationServiceInterface {
    
    /**
     * @inheritdoc
     */
    public function createClientId(Client $client) {
        if ($client === null) {
            throw new \InvalidArgumentException("No client was provided.");
        }
        $information = $client->getClientType()
                .$client->getRedirectionUri()
                .$client->getAppTitle()
                .$client->getEmail();
        $id = hash('sha256', $information, true);
        
        return base64_encode($id);
    }
}
