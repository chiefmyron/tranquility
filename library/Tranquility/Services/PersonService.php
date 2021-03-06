<?php namespace Tranquility\Services;

use \Tranquility\Utility;
use \Tranquility\Enums\System\EntityType   as EnumEntityType;
use \Tranquility\Enums\System\MessageLevel as EnumMessageLevel;

class PersonService extends \Tranquility\Services\Service {
    /**
     * Specify business object name
     *
     * @return string
     */
    public function businessObject() {
        return 'Tranquility\Data\Objects\BusinessObjects\PersonBusinessObject';
    }
	
	/**
	 * Create a new Person record
	 *
	 * @param array Data for creating a new Person record
	 * @return \Tranquility\Services\ServiceResponse
	 */
	public function create(array $data) {
        $accountId = Utility::extractValue($data, 'accountId', '');
        
        // If an associated account has been provided, associate it now
        if ($accountId != '') {
            $response = $this->findEntity($accountId);
            if ($response->containsErrors()) {
                return $response;
            }
            $data['account'] = $response->getFirstContentItem();
        }
        
        // Create new Person record
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
        $accountId = Utility::extractValue($data, 'accountId', '');
        
        // If an associated account has been provided, associate it now
        if ($accountId != '') {
            $response = $this->findEntity($accountId);
            if ($response->containsErrors()) {
                return $response;
            }
            $data['account'] = $response->getFirstContentItem();
        }

        // Update existing Person record
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
	 * Person specific validation of inputs
	 * 
	 * @param array   $inputs     Array of data field values
	 * @param boolean $newRecord  True if creating validating fields for a new record
	 * @return array  Error messages from validation. Empty array if no errors.
	 */
	public function validateBusinessObjectRules($inputs, $newRecord) {
		$messages = array();

        // Check that the account is valid (if provided)
		if (isset($inputs['account']) && !is_a($inputs['account'], '\Tranquility\Data\Objects\BusinessObjects\AccountBusinessObject')) {
			$messages[] = array(
				'code' => 10024,
				'text' => 'message_10024_person_invalid_account',
				'level' => EnumMessageLevel::Error,
				'fieldId' => 'accountId'
			);
		}
		
		// TODO: Validate title code against reference data table
		
		return $messages;
	}
}	