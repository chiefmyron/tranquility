<?php namespace Tranquility\Services;

use \Tranquility\Utility                   as Utility;
use \Tranquility\Enums\System\EntityType   as EnumEntityType;
use \Tranquility\Enums\System\MessageLevel as EnumMessageLevel;

class AddressService extends \Tranquility\Services\Service {
    /**
     * Specify business object name
     *
     * @return string
     */
    public function businessObject() {
        return 'Tranquility\Data\BusinessObjects\AddressPhysicalBusinessObject';
    }
	
	/**
	 * Create a new Address record
	 *
	 * @param array Data for creating a new Address record
	 * @return \Tranquility\Services\ServiceResponse
	 */
	public function create(array $data) {
		$response = parent::create($data);
		
		// Add entity specific success code
		if (!$response->containsErrors()) {
			$response->addMessage(10020, EnumMessageLevel::Success, 'message_10020_person_record_created_successfully');
		}
		
		return $response;
	}
	
	/**
	 * Updates an existing Address record
	 *
	 * @param int   $id    ID for existing Address record
	 * @param array $data  Data for updating an existing Address record
	 * @return \Tranquility\Services\ServiceResponse
	 */
	public function update($id, array $data) {
		$response = parent::update($id, $data);
		
		// Add entity specific success code
		if (!$response->containsErrors()) {
			$response->addMessage(10021, EnumMessageLevel::Success, 'message_10021_person_record_updated_successfully');
		}
		
		return $response;
	}
    
    /** Deletes an existing Address record
     *
     * @param int   $id                ID for existing Address record
     * @param array $auditTrailFields  Array containing audit trail information
     * @return \Tranquility\Services\ServiceResponse
     */
    public function delete($id, array $auditTrailFields) {
		// Attempt to update the entity
        $response = parent::delete($id, $auditTrailFields);
        
        // Add entity specific success code
        if (!$response->containsErrors()) {
			$response->addMessage(10022, EnumMessageLevel::Success, 'message_10022_person_record_deleted_successfully');
		}
        
		return $response;
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