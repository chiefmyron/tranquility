<?php namespace Tranquility\Services;

use \Tranquility\Utility                   as Utility;
use \Tranquility\Enums\System\EntityType   as EnumEntityType;
use \Tranquility\Enums\System\MessageLevel as EnumMessageLevel;

class User extends \Tranquility\Services\Service {
	// Fields for a User entity
	protected $_entityFields = array(
		'username',
		'password',
		'timezoneCode',
		'localeCode',
		'active',
		'securityGroupId',
		'registeredDateTime'
	);
	
	// Mandatory fields for a User entity
	protected $_entityMandatoryFields = array(
		'username',
		'timezoneCode',
		'localeCode',
		'active',
		'securityGroupId'	
	);
	
	// Additional mandatory fields for creating a new User entity
	protected $_newEntityMandatoryFields = array(
		'password', 
		'passwordConfirm', 
		'parentId'
	);

	/** 
	 * Specify actual model name
	 *
	 * @return mixed
	 */
	public function model() {
		return 'Tranquility\Models\User';
	}
	
	/**
	 * Get a list of data fields associated with the User
	 * 
	 * @return array
	 */
	protected function _getFields() {
		return array_merge(parent::_getFields(), $this->_entityFields);
	}
	
	/**
	 * Get a list of fields that are mandatory for creating / updating a User entity
	 * 
	 * @return array
	 */
	protected function _getMandatoryFields($newRecord = false) {
		$fields = array_merge(parent::_getMandatoryFields($newRecord), $this->_entityMandatoryFields);
		if ($newRecord) {
			$mandatoryFields = array_merge($fields, $this->_newEntityMandatoryFields);
		}
		return $mandatoryFields;
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
}	