<?php namespace Tranquility\Services;

use \Tranquility\Utility                   as Utility;
use \Tranquility\Enums\System\EntityType   as EnumEntityType;
use \Tranquility\Enums\System\MessageLevel as EnumMessageLevel;

class AddressElectronicService extends \Tranquility\Services\Service {
    /**
     * Specify business object name
     *
     * @return string
     */
    public function businessObject() {
        return 'Tranquility\Data\BusinessObjects\AddressElectronicBusinessObject';
    }
	
	/**
	 * Create a new electronic address record
	 *
	 * @param array Data for creating a new electronic address record
	 * @return \Tranquility\Services\ServiceResponse
	 */
	public function create(array $data) {
		$response = parent::create($data);
		
		// Add entity specific success code
		if (!$response->containsErrors()) {
			$response->addMessage(10046, EnumMessageLevel::Success, 'message_10046_electronic_address_record_created_successfully');
		}
		
		return $response;
	}
	
	/**
	 * Updates an existing electronic address record
	 *
	 * @param int   $id    ID for existing electronic address record
	 * @param array $data  Data for updating an existing electronic address record
	 * @return \Tranquility\Services\ServiceResponse
	 */
	public function update($id, array $data) {
		$response = parent::update($id, $data);
		
		// Add entity specific success code
		if (!$response->containsErrors()) {
			$response->addMessage(10047, EnumMessageLevel::Success, 'message_10047_electronic_address_record_updated_successfully');
		}
		
		return $response;
	}
    
    /** Deletes an existing electronic address record
     *
     * @param int   $id                ID for existing electronic address record
     * @param array $auditTrailFields  Array containing audit trail information
     * @return \Tranquility\Services\ServiceResponse
     */
    public function delete($id, array $auditTrailFields) {
		// Attempt to update the entity
        $response = parent::delete($id, $auditTrailFields);
        
        // Add entity specific success code
        if (!$response->containsErrors()) {
			$response->addMessage(10048, EnumMessageLevel::Success, 'message_10048_electronic_address_record_deleted_successfully');
		}
        
		return $response;
    }
}	