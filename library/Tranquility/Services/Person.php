<?php namespace Tranquility\Services;

use \Tranquility\Utility                   as Utility;
use \Tranquility\Enums\System\EntityType   as EnumEntityType;
use \Tranquility\Enums\System\MessageLevel as EnumMessageLevel;

class Person extends \Tranquility\Services\Service {
	// Fields for a Person entity
	protected $_entityFields = array(
		'title',
		'firstName',
		'lastName',
		'position'
	);
	
	// Mandatory fields for a Person entity
	protected $_entityMandatoryFields = array(
		'firstName',
		'lastName'	
	);

	/** 
	 * Specify actual model name
	 *
	 * @return mixed
	 */
	public function model() {
		return 'Tranquility\Models\Person';
	}
	
	/**
	 * Create a new Person record
	 *
	 * @param array Data for creating a new Person record
	 * @return \Tranquility\Services\ServiceResponse
	 */
	public function create(array $data) {
		$response = parent::create($data);
		
		// Add entity specific success code
		if (!$response->containsErrors()) {
			$response->addMessage(10020, 'message_10020_person_record_created_successfully', EnumMessageLevel::Success);
		}
		
		return $response;
	}
	
	/**
	 * Updates an existing Person record
	 *
	 * @param int   $id    ID for existing Person record
	 * @param array $data  Data for updating an existing Person record
	 * @return \Tranquility\Services\ServiceResponse
	 */
	public function update($id, array $data) {
		$response = parent::update($id, $data);
		
		// Add entity specific success code
		if (!$response->containsErrors()) {
			$response->addMessage(10021, 'message_10021_person_record_updated_successfully', EnumMessageLevel::Success);
		}
		
		return $response;
	}
	
	/**
	 * Get a list of data fields associated with the Person
	 * 
	 * @return array
	 */
	protected function _getFields() {
		return array_merge(parent::_getFields(), $this->_entityFields);
	}
	
	/**
	 * Get a list of fields that are mandatory for creating / updating a Person entity
	 * 
	 * @return array
	 */
	protected function _getMandatoryFields($newRecord = false) {
		return array_merge(parent::_getMandatoryFields($newRecord), $this->_entityMandatoryFields);
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
		
		// TODO: Validate title code against reference data table
		
		// If there are one or more messages, then there are errors - return messages
		if (count($messages) > 0) {
			return $messages;
		}
		
		return true;
	}
}	