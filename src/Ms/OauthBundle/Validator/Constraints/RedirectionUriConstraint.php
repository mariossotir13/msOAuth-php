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
    public $message = 'Please, remove the fragment portion from the redirection URI: {{ value }}.';
}
