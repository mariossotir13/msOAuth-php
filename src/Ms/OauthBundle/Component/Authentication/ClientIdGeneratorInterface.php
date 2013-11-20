<?php

namespace Ms\OauthBundle\Component\Authentication;

/**
 * Δημιουργεί Αναγνωριστικά Πελατών.
 */
interface ClientIdGeneratorInterface {
    
    /**
     * Δημιοιυργεί το αναγνωριστικό ενός πελάτη.
     * 
     * @param Client $client Ο πελάτης για τον οποίο θέλουμε να δημιουργήσουμε
     * κωδικό.
     * @return string Το αναγνωριστικό για τον πελάτη `$client`.
     * @throws \InvalidArgumentException Εάν η `$client` είναι null.
     */
    public function generate($client);
}
