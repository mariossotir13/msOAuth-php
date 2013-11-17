<?php

namespace Ms\OauthBundle\Component\Authentication;

use Ms\OauthBundle\Entity\Client;

/**
 *
 * @author user
 */
interface AuthenticationServiceInterface {
    
    /**
     * Creates an id for a new client.
     * 
     * @param Client $client The Client whose id this method generates.
     * @return string The id of the `$client`.
     */
    public function createClientId(Client $client); 
}
