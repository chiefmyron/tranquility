<?php namespace Tranquility\Services;

use \Tranquility\Utility                   as Utility;
use \Tranquility\Enums\System\EntityType   as EnumEntityType;
use \Tranquility\Enums\System\MessageLevel as EnumMessageLevel;

class AccountService extends \Tranquility\Services\Service {
    /**
     * Specify business object name
     *
     * @return string
     */
    public function businessObject() {
        return 'Tranquility\Data\Objects\BusinessObjects\AccountBusinessObject';
    }
	
	/**
	 * Create a new Account record
	 *
	 * @param array Data for creating a new Account record
	 * @return \Tranquility\Services\ServiceResponse
	 */
	public function create(array $data) {
		$response = parent::create($data);
		
		// Add entity specific success code
		if (!$response->containsErrors()) {
			$response->addMessage(10070, EnumMessageLevel::Success, 'message_10070_account_record_created_successfully');
		}
		
		return $response;
	}
	
	/**
	 * Updates an existing Account record
	 *
	 * @param int   $id    ID for existing Account record
	 * @param array $data  Data for updating an existing Account record
	 * @return \Tranquility\Services\ServiceResponse
	 */
	public function update($id, array $data) {
		$response = parent::update($id, $data);
		
		// Add entity specific success code
		if (!$response->containsErrors()) {
			$response->addMessage(10071, EnumMessageLevel::Success, 'message_10071_account_record_updated_successfully');
		}
		
		return $response;
	}
    
    /** Deletes an existing Account record
     *
     * @param int   $id                ID for existing Account record
     * @param array $auditTrailFields  Array containing audit trail information
     * @return \Tranquility\Services\ServiceResponse
     */
    public function delete($id, array $auditTrailFields) {
		// Attempt to update the entity
        $response = parent::delete($id, $auditTrailFields);
        
        // Add entity specific success code
        if (!$response->containsErrors()) {
			$response->addMessage(10072, EnumMessageLevel::Success, 'message_10072_account_record_deleted_successfully');
		}
        
		return $response;
    }
    
    /** 
     * Deletes multiple Account records in a single operation
     * 
     * @param array $personIds         Array of Account record IDs to mark as deleted
     * @param array $auditTrailFields  Array containing audit trail information (will be used against all Account records being deleted)
     * @return \Tranquility\Services\ServiceResponse
     */
    public function deleteMultiple(array $accountIds, array $auditTrailFields) {
        $response = new ServiceResponse();
        
        // Check that at least one ID has been supplied
        if (count($accountIds) <= 0) {
            $response->addMessage(10002, EnumMessageLevel::Error, 'message_10002_mandatory_input_field_missing');
            return $response;
        }
        
        // Delete each Account record individually
        $successCounter = 0;
        foreach ($accountIds as $id) {
            $deleteResponse = $this->delete($id, $auditTrailFields);
            if ($deleteResponse->containsErrors()) {
                $response->addMessages($deleteResponse->getMessages());
            } else {
                $successCounter++;
            }
        }
        
        // If at least one record was deleted successfully, add success message
        if ($successCounter > 0) {
            $response->addMessage(10073, EnumMessageLevel::Success, 'message_10073_account_multiple_records_deleted_successfully', ['count' => $successCounter]);
        }
        
        return $response;
    }
	
	/**
	 * Account specific validation of inputs
	 * 
	 * @param array   $inputs     Array of data field values
	 * @param boolean $newRecord  True if creating validating fields for a new record
	 * @return array  Error messages from validation. Empty array if no errors.
	 */
	public function validateBusinessObjectRules($inputs, $newRecord) {
		$messages = array();
		
		// TODO: Validate title code against reference data table
		
		return $messages;
	}
}	