<?php namespace Tranquility\Services;

use \Tranquility\Utility                                    as Utility;
use \Tranquility\Enums\System\EntityType                    as EnumEntityType;
use \Tranquility\Enums\System\MessageLevel                  as EnumMessageLevel;
use \Tranquility\Enums\System\HttpStatusCode                as EnumHttpStatusCode;
use \Tranquility\Enums\BusinessObjects\Address\AddressTypes as EnumAddressType;

class AddressService extends \Tranquility\Services\Service {
    /**
     * Specify business object name
     *
     * @return string
     */
    public function businessObject() {
        return 'Tranquility\Data\Objects\BusinessObjects\AddressBusinessObject';
    }
	
	/**
	 * Create a new address record
	 *
	 * @param array Data for creating a new address record
	 * @return \Tranquility\Services\ServiceResponse
	 */
	public function create(array $data) {
        $parentId = Utility::extractValue($data, 'parentId', 0);
		$category = Utility::extractValue($data, 'category', null);
        
		// Retrieve parent entity
		$response = $this->findEntity($parentId);
        if ($response->containsErrors()) {
            return $response;
        }

		// If parent entity does not have any addresses in this category already, this address becomes primary by default
		$data['parent'] = $response->getFirstContentItem();
		if (count($data['parent']->getAddresses($category)) <= 0) {
			$data['primaryContact'] = true;
		}

		// Set any empty strings to nulls
        foreach ($data as $key => $value) {
            if ($value === '') {
                $data[$key] = null;
            }
        }
				
		// Perform input validation
		$response = new ServiceResponse();
		$validation = $this->validateInputFields($data, true);
		if ($validation !== true) {
			// Send error response back immediately
			$response->addMessages($validation);
			$response->setHttpResponseCode(EnumHttpStatusCode::BadRequest);
			return $response;
		}

		// Check if address already exists for the parent entity
		$criteria = array(
			['addressText', '=', $data['addressText']],
			['addressType', '=', $data['addressType']],
			['parentEntity', '=', $data['parent']]
		);
		$searchResponse = $this->all($criteria);
		if ($searchResponse->getItemCount() > 0) {
			// Address already exists - no need to create
			$searchResponse->addMessage(10052, EnumMessageLevel::Warning, 'message_10052_address_already_exists');
			return $searchResponse;
		}

		// Attempt to create the entity
        $entity = $this->_getRepository()->create($data);
		$response->setContent($entity);
		$response->setHttpResponseCode(EnumHttpStatusCode::OK);
		if ($category == EnumAddressType::Email) {
			$response->addMessage(10046, EnumMessageLevel::Success, 'message_10046_electronic_address_record_created_successfully');
		} else {
			$response->addMessage(10043, EnumMessageLevel::Success, 'message_10043_phone_address_record_created_successfully');
		}
		
		return $response;
	}
	
	/**
	 * Updates an existing address record
	 *
	 * @param int   $id    ID for existing address record
	 * @param array $data  Data for updating an existing address record
	 * @return \Tranquility\Services\ServiceResponse
	 */
	public function update($id, array $data) {
		$response = parent::update($id, $data);
		
		// Add entity specific success code
		if (!$response->containsErrors()) {
			if ($data['category'] == EnumAddressType::Email) {
				$response->addMessage(10047, EnumMessageLevel::Success, 'message_10047_electronic_address_record_updated_successfully');
			} else {
				$response->addMessage(10044, EnumMessageLevel::Success, 'message_10044_phone_address_record_updated_successfully');
			}
		}
		
		return $response;
	}
    
    /** 
     * Deletes an existing address record
     *
     * @param int   $id                ID for existing address record
     * @param array $auditTrailFields  Array containing audit trail information
     * @return \Tranquility\Services\ServiceResponse
     */
    public function delete($id, array $auditTrailFields) {
		// Attempt to update the entity
        $response = parent::delete($id, $auditTrailFields);
        
        // Add entity specific success code
        if (!$response->containsErrors()) {
			$category = $response->getFirstContentItem()->category;
			if ($category == EnumAddressType::Email) {
				$response->addMessage(10048, EnumMessageLevel::Success, 'message_10048_electronic_address_record_deleted_successfully');
			} else {
				$response->addMessage(10045, EnumMessageLevel::Success, 'message_10045_phone_address_record_deleted_successfully');
			}
		}
        
		return $response;
    }
    
    /**
     * Marks an address record as the primary contact mechanism. Any existing primary
     * address records will have the flag removed.
     *
     * @param int   $id                ID for existing address record
     * @param array $auditTrailFields  Array containing audit trail information
     * @return \Tranquility\Services\ServiceResponse
     */
    public function makePrimary($id, array $auditTrailFields) {
        // Set up response object
		$response = new ServiceResponse();
		
		// Perform input validation
		$validation = $this->validateAuditTrailFields($auditTrailFields);
		if (count($validation) > 0) {
			// Send error response back immediately
			$response->addMessages($validation);
			$response->setHttpResponseCode(EnumHttpStatusCode::BadRequest);
			return $response;
		}
		
		// Attempt to update the entity
        $entity = $this->_getRepository()->makePrimary($id, $auditTrailFields);
		$response->setContent($entity);
		$response->setHttpResponseCode(EnumHttpStatusCode::OK);
        $response->addMessage(10049, EnumMessageLevel::Success, 'message_10049_phone_address_primary_contact_updated', ['addressText' => $entity->addressText]);
		return $response;
    }
    
    /**
	 * Address specific validation of inputs
	 * 
	 * @param array   $inputs     Array of data field values
	 * @param boolean $newRecord  True if creating validating fields for a new record
	 * @return array  Error messages from validation. Empty array if no errors.
	 */
	public function validateBusinessObjectRules($inputs, $newRecord) {
		$messages = array();
		
		// If this is an email address, validate syntax
        $category = Utility::extractValue($inputs, 'category', null);
        $addressText = Utility::extractValue($inputs, 'addressText', null);
        if ($category == EnumAddressType::Email && !filter_var($addressText, FILTER_VALIDATE_EMAIL)) {
			$messages[] = array(
				'code' => 10051,
				'text' => 'message_10051_email_address_format_invalid',
				'level' => EnumMessageLevel::Error,
				'fieldId' => 'addressText'
			);
        }
		
		return $messages;
	}
}	