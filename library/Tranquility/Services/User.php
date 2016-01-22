<?php namespace Tranquility\Services;

use \Tranquility\Utility                   as Utility;
use \Tranquility\Enums\System\EntityType   as EnumEntityType;
use \Tranquility\Enums\System\MessageLevel as EnumMessageLevel;

class User extends \Tranquility\Services\Service {
	/** 
	 * Specify actual model name
	 *
	 * @return mixed
	 */
	public function model() {
		return 'Tranquility\Models\User';
	}
    
    /**
     * Specify actual business object class name
     *
     * @return string
     */
    public function businessObject() {
        return 'Tranquility\BusinessObjects\User';
    }
	
	/**
	 * Validate data for input fields - this includes checking mandatory fields and audit
	 * trail fields
	 * 
	 * @param array   $inputs    Array of data field values
	 * @param boolean $newRecord True if creating validating fields for a new record
	 * @return mixed  True if valid input, array of messages if invalid input
	 */
	public function validateInputFields($inputs, $newRecord = false) {
		$messages = array();
		
		// Perform mandatory field and audit trail field validation
		$result = parent::validateInputFields($inputs, $newRecord);
		if ($result !== true) {
			$messages = $result;
		}
		
		// Password verification
		if ($newRecord) {
			// Check password and password confirmation match
			if ($inputs['password'] != $inputs['passwordConfirm']) {
				// Passwords must match
				$messages[] = array(
					'code' => 10003,
					'text' => 'message_10003_passwords_must_match',
					'level' => EnumMessageLevel::Error,
					'fieldId' => 'passwordConfirm'
				);
			}
			
			// Check password minimum length
			
		}
		
		// If there are one or more messages, then there are errors - return messages
		if (count($messages) > 0) {
			return $messages;
		}
		
		return true;
	}
	
	public function findByToken($id, $token) {
		$entity = $this->model->findByToken($id, $token);
		return $this->_findResponse($entity);
	}
	
	public function updateRememberToken($id, $token) {
		$this->model->updateRememberToken($id, $token);
	}
}	