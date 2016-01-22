<?php namespace Tranquility\Services;

use \Tranquility\Utility                   as Utility;
use \Tranquility\Enums\System\EntityType   as EnumEntityType;
use \Tranquility\Enums\System\MessageLevel as EnumMessageLevel;

class Person extends \Tranquility\Services\Service {
	/** 
	 * Specify actual model name
	 *
	 * @return string
	 */
	public function model() {
		return 'Tranquility\Models\Person';
	}
    
    /**
     * Specify business object name
     *
     * @return string
     */
    public function businessObject() {
        return 'Tranquility\BusinessObjects\Person';
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
			$response->addMessage(10020, EnumMessageLevel::Success, 'message_10020_person_record_created_successfully');
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
			$response->addMessage(10021, EnumMessageLevel::Success, 'message_10021_person_record_updated_successfully');
		}
		
		return $response;
	}
    
    /** Deletes an existing Person record
     *
     * @param int   $id                ID for existing Person record
     * @param array $auditTrailFields  Array containing audit trail information
     * @return \Tranquility\Services\ServiceResponse
     */
    public function delete($id, array $auditTrailFields) {
        // Attempt to remove associated user account
        // TODO
		
		// Attempt to update the entity
        $response = parent::delete($id, $auditTrailFields);
        
        // Add entity specific success code
        if (!$response->containsErrors()) {
			$response->addMessage(10022, EnumMessageLevel::Success, 'message_10022_person_record_deleted_successfully');
		}
        
		return $response;
    }
    
    /** 
     * Deletes multiple Person records in a single operation
     * 
     * @param array $personIds         Array of Person record IDs to mark as deleted
     * @param array $auditTrailFields  Array containing audit trail information (will be used against all Person records being deleted)
     * @return \Tranquility\Services\ServiceResponse
     */
    public function deleteMultiple(array $personIds, array $auditTrailFields) {
        $response = new ServiceResponse();
        
        // Check that at least one ID has been supplied
        if (count($personIds) <= 0) {
            $response->addMessage(10002, EnumMessageLevel::Error, 'message_10002_mandatory_input_field_missing');
            return $response;
        }
        
        // Delete each person record individually
        $successCounter = 0;
        foreach ($personIds as $id) {
            $deleteResponse = $this->delete($id, $auditTrailFields);
            if ($deleteResponse->containsErrors()) {
                $response->addMessages($deleteResponse->getMessages());
            } else {
                $successCounter++;
            }
        }
        
        // If at least one record was deleted successfully, add success message
        if ($successCounter > 0) {
            $response->addMessage(10023, EnumMessageLevel::Success, 'message_10023_person_multiple_records_deleted_successfully', ['count' => $successCounter]);
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