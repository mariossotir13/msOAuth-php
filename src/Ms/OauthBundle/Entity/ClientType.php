<?php

namespace Ms\OauthBundle\Entity;

/**
 * Ορίζει τους δυνατούς τύπους ενός πελάτη.
 *
 * @author user
 */
class ClientType {
    /**#@+
     * Ακέραιες σταθερές οι οποίες αντιστοιχούν στους δυνατούς τύπους ενός πελάτη.
     * 
     * @var int. 
     */
    const TYPE_CONFIDENTIAL = 1;
    const TYPE_PUBLIC = 2;
    /**#@-*/
}
