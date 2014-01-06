<?php

namespace Ms\OauthBundle\Component\Authorization;

use Symfony\Component\Validator\ConstraintViolationListInterface;
use Ms\OauthBundle\Component\Authorization\AuthorizationError;

/**
 * Description of ValidationResponse
 *
 * @author Marios
 */
class ValidationResponse {
    
    /**
     *
     * @var string[]
     */
    protected static $propertyPathToErrorMap = array(
        'redirectionUri' => AuthorizationError::REDIRECTION_URI,
        'responseType' => AuthorizationError::UNSUPPORTED_RESPONSE_TYPE,
        'scopes' => AuthorizationError::INVALID_SCOPE
    );
    
    /**
     *
     * @var string
     */
    private $error;
    
    /**
     *
     * @var string
     */
    private $errorMessage = '';
    
    /**
     *
     * @var boolean
     */
    private $valid = true;
    
    /**
     *
     * @var ConstraintViolationListInterface
     */
    private $violationsList;
    
    /**
     * 
     * @param ConstraintViolationListInterface $violationsList
     */
    function __construct(ConstraintViolationListInterface $violationsList) {
        $this->setViolationsList($violationsList);
        $this->setError($violationsList);
    }

    /**
     * 
     * @return string
     */
    public function getError() {
        return $this->error;
    }

    /**
     * 
     * @return string
     */
    public function getErrorMessage() {
        return $this->errorMessage;
    }

    /**
     * 
     * @return boolean
     */
    public function isValid() {
        return $this->valid;
    }
    
    /**
     * 
     * @param ConstraintViolationListInterface $violationsList
     * @return void
     */
    protected function setError(ConstraintViolationListInterface $violationsList) {
        if ($violationsList->count() === 0) {
            return;
        }
        
        /* @var $violation \Symfony\Component\Validator\ConstraintViolationInterface */
        $violation = $violationsList->get(0);
        $property = $violation->getPropertyPath();
        
        $this->error = isset(static::$propertyPathToErrorMap[$property]) 
            ? static::$propertyPathToErrorMap[$property]
            : AuthorizationError::INVALID_REQUEST;
        $this->errorMessage = $violation->getMessage();
        $this->valid = false;
    }
    
    /**
     * 
     * @param ConstraintViolationListInterface $violationsList
     * @return void
     */
    protected function setViolationsList(ConstraintViolationListInterface $violationsList) {
        if ($violationsList === null) {
            throw new \InvalidArgumentException('No violations list was specified.');
        }
        $this->violationsList = $violationsList;
    }
}
