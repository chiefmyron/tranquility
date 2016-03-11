<?php namespace Tranquility\Services;

use \Tranquility\Utility                        as Utility;
use \Tranquility\Enums\System\EntityType        as EnumEntityType;
use \Tranquility\Enums\System\MessageLevel      as EnumMessageLevel;
use \Tranquility\Enums\System\HttpStatusCode    as EnumHttpStatusCode;

class TagService extends \Tranquility\Services\Service {
    /**
     * Specify business object name
     *
     * @return string
     */
    public function businessObject() {
        return 'Tranquility\Data\BusinessObjects\Extensions\Tags';
    }
	
	/**
	 * Create a new tag
	 *
	 * @param array Data for creating a new tag
	 * @return \Tranquility\Services\ServiceResponse
	 */
	public function create(array $data) {
		$response = parent::create($data);
		
		// Add entity specific success code
		if (!$response->containsErrors()) {
			$response->addMessage(10043, EnumMessageLevel::Success, 'message_10043_phone_address_record_created_successfully');
		}
		
		return $response;
	}
	
	/**
	 * Updates an existing tag
	 *
	 * @param int   $id    ID for existing tag record
	 * @param array $data  Data for updating an existing tag record
	 * @return \Tranquility\Services\ServiceResponse
	 */
	public function update($id, array $data) {
		$response = parent::update($id, $data);
		
		// Add entity specific success code
		if (!$response->containsErrors()) {
			$response->addMessage(10044, EnumMessageLevel::Success, 'message_10044_phone_address_record_updated_successfully');
		}
		
		return $response;
	}
    
    /** 
     * Deletes an existing tag record
     *
     * @param int   $id                ID for existing tag record
     * @param array $auditTrailFields  Array containing audit trail information
     * @return \Tranquility\Services\ServiceResponse
     */
    public function delete($id, array $auditTrailFields) {
		// Attempt to update the entity
        $response = parent::delete($id, $auditTrailFields);
        
        // Add entity specific success code
        if (!$response->containsErrors()) {
			$response->addMessage(10045, EnumMessageLevel::Success, 'message_10045_phone_address_record_deleted_successfully');
		}
        
		return $response;
    }
    
    public function setEntityTags($entityId, $tagValues, array $auditTrailFields) {
        // Get the set of tags already created
        $this->all(...);
        
        // Create new tags for those that don't already exist
        $tags = array();
        
        // Get parent entity
        $entity = $this->findParentEntity($entityId);
        
        // Update tags for entity
        $entity->setTags($tags);
        
        // Add entity specific success code
        if (!$response->containsErrors()) {
			$response->addMessage(10045, EnumMessageLevel::Success, 'message_10045_phone_address_record_deleted_successfully');
		}
        
		return $response;
        
    }
}	