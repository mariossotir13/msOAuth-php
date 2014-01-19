<?php

namespace Ms\OauthBundle\Component\Security;

use Ms\OauthBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Description of MsOauthUserAuthenticator
 *
 * @author Marios
 */
class MsOauthUserAuthenticator extends MsOauthAuthenticator {

    /**
     * @inheritdoc
     */
    protected function validateUser(User $user, TokenInterface $token) {
        // TODO
        return true;
    }
}
