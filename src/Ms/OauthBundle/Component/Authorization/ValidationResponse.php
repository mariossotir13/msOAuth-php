<?php

namespace Ms\OauthBundle\Component\Authorization;

use Symfony\Component\Validator\ConstraintViolationListInterface;
use Ms\OauthBundle\Component\Authorization\AuthorizationError;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\ConstraintViolation;

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
        'code' => AuthorizationError::INVALID_GRANT,
        'clientId' => AuthorizationError::INVALID_CLIENT,
        'redirectionUri' => AuthorizationError::REDIRECTION_URI,
        'responseType' => AuthorizationError::UNSUPPORTED_RESPONSE_TYPE,
        'scopes' => AuthorizationError::INVALID_SCOPE,
        'grantType' => AuthorizationError::UNSUPPORTED_GRANT_TYPE
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
     * @var string[string]
     */
    private $propertyToErrorMap = array();
    
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
     * @param array $violations
     * @return ValidationResponse
     */
    public static function fromArray(array $violations) {
        $violationsList = static::createViolationsListFromArray($violations);
        
        return new ValidationResponse($violationsList);
    }
    
    /**
     * 
     * @param array $violations
     * @return ConstraintViolationList
     */
    protected static function createViolationsListFromArray(array $violations) {
        $violationsList = new ConstraintViolationList();
        foreach ($violations as $violationArray) {
            $violationsList->add(new ConstraintViolation(
                $violationArray['message'],
                $violationArray['messageTemplate'],
                $violationArray['messageParameters'],
                $violationArray['root'],
                $violationArray['propertyPath'],
                $violationArray['invalidValue']
            ));
        }
        
        return $violationsList;
    }
    
    /**
     * 
     * @param ConstraintViolationListInterface $violationsList
     * @param string[string] $propertyToErrorMap
     */
    function __construct(ConstraintViolationListInterface $violationsList,
            array $propertyToErrorMap = array()) {
        $this->propertyToErrorMap = $propertyToErrorMap;
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
     * @return string[string]
     */
    public function getPropertyToErrorMap() {
        return $this->propertyToErrorMap;
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
        
        $this->error = isset($this->propertyToErrorMap[$property]) 
            ? $this->propertyToErrorMap[$property]
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
