<?php

namespace Ms\OauthBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraints\Url;

/**
 * Description of RedirectionUriConstraint
 *
 * @author Marios
 * @Annotation
 */
class RedirectionUriConstraint extends Url {
    
    /**
     * @inhderitdoc
     */
    public $message = 'A redirection URI cannot contain a "fragment" portion.';
}
