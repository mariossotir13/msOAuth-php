<?php

namespace Ms\OauthBundle\Component\Authentication;

/**
 * Δημιουργεί Αναγνωριστικά Πελατών.
 *
 * @author user
 */
class MsOauthClientIdGenerator implements ClientIdGeneratorInterface {

    /**
     * @inheritdoc
     */
    public function generate(Client $client) {
        if ($client === null) {
            throw new \InvalidArgumentException("No client was provided.");
        }
        
        $information = $client->getClientType()
                . $client->getRedirectionUri()
                . $client->getAppTitle()
                . $client->getEmail();
        $id = hash('sha256', $information, true);

        return trim(base64_encode($id), '=');
    }
}
