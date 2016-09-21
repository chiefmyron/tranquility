<?php namespace Tranquility\Services;

use Illuminate\Support\Facades\Log;

use \Tranquility\Utility                        as Utility;
use \Tranquility\Enums\System\EntityType        as EnumEntityType;
use \Tranquility\Enums\System\MessageLevel      as EnumMessageLevel;
use \Tranquility\Enums\System\HttpStatusCode    as EnumHttpStatusCode;

use Tranquility\Data\Objects\BusinessObjects\BusinessObject as Entity;

class TagService extends \Tranquility\Services\Service {
    /**
     * Specify business object name
     *
     * @return string
     */
    public function businessObject() {
        return 'Tranquility\Data\Objects\ExtensionObjects\Tag';
    }
    
    /**
	 * Retrieve all entities of this type
	 *
	 * @param int $resultsPerPage If zero or less, or null, the full result set will be returned
	 * @param int $startRecordIndex Index of the record to start the result set from. Defaults to zero.
	 * @param array $filter Used to specify additional filters to the set of results
	 * @param array $order Used to specify order parameters to the set of results
	 * @return \Tranquility\Service\ServiceResponse
	 */
	public function all($filterConditions = array(), $orderConditions = array(), $resultsPerPage = 0, $startRecordIndex = 0) {
        // Convert result set into array of business objects
        $businessObjects = $this->_getRepository()->all($filterConditions, $orderConditions, $resultsPerPage, $startRecordIndex);
		
		// If no results are returned, add a warning message to the response
		$messages = array();
		if (count($businessObjects) <= 0) {
			$messages[] = array(
				'code' => 10000,
				'text' => 'message_10000_no_records_returned',
				'level' => EnumMessageLevel::Warning
			);
		}
		
		// Set up the response message
		$response = new ServiceResponse(array(
			'content' => $businessObjects,
			'messages' => $messages,
			'responseCode' => EnumHttpStatusCode::OK
		));
		return $response;
	}
	
	/**
	 * Create a new tag
	 *
	 * @param array Data for creating a new tag
	 * @return \Tranquility\Services\ServiceResponse
	 */
	public function create(array $data) {
		// Set up response object
		$response = new ServiceResponse();
        
        // Check that tag text is provided
        $messages = array();
        $text = strtolower(Utility::extractValue($data, 'text', ''));
        if ($text == '') {
            $response->addMessage(10002, EnumMessageLevel::Error, 'message_10002_mandatory_input_field_missing', array(), 'text');
            $response->setHttpResponseCode(EnumHttpStatusCode::BadRequest);
            return $response;
        }
                
		// Attempt to create the tag
        Log::info('Creating new tag "'.$text.'"');
        $data['text'] = $text;
        $entity = $this->_getRepository()->create($data);
		$response->setContent($entity);
		$response->setHttpResponseCode(EnumHttpStatusCode::OK);
        $response->addMessage(10060, EnumMessageLevel::Success, 'message_10060_new_tag_created');
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
			$response->addMessage(10061, EnumMessageLevel::Success, 'message_10061_tag_deleted');
		}
        
		return $response;
    }
    
    public function setEntityTags($entityId, $tagValues) {
        // Set up response object
		$response = new ServiceResponse();

        // Format tag values
        $formattedTags = array();
        foreach ($tagValues as $text) {
            $text = trim(str_replace(array(',', ';'), '', $text));
            if ($text !== '') {
                $formattedTags[] = $text;
            }
        }

        // Set up the finalised list of tags for the entity
        $entityTagSet = array();

        // Get the set of tags objects that already exist based on the tag text provided
        $existingTagText = array();
        if (count($formattedTags) > 0) {
            $filter = array(['text', 'IN', $formattedTags]);
            $result = $this->all($filter)->getContent();
            foreach($result as $tag) {
                $entityTagSet[] = $tag;
                $existingTagText[] = $tag->text;
            }
        }

        // Create new tag objects for those that don't already exist
        foreach ($formattedTags as $text) {
            if (!in_array($text, $existingTagText)) {
                $result = $this->create(array('text' => $text));
                $entityTagSet[] = $result->getFirstContentItem();
            }
        }
        
		// Replace tag collection for entity
        $repository = $this->_entityManager->getRepository(Entity::class);
        $entity = $repository->setTags($entityId, $entityTagSet);
		$response->setContent($entity);
		$response->setHttpResponseCode(EnumHttpStatusCode::OK);
        $response->addMessage(10062, EnumMessageLevel::Success, 'message_10062_tag_collection_updated');
		return $response;
    }
    
    public function removeTag($entityId, $tagId) {
        // Set up response object
        $response = new ServiceResponse;
        
        // Find existing tag
        $result = $this->find($tagId);
        if ($result->containsErrors()) {
            return $result;
        }
        
        // Remove tag from entity
        $tag = $result->getFirstContentItem();
        $repository = $this->_entityManager->getRepository(Entity::class);
        $entity = $repository->removeTag($entityId, $tag);
        $response->setContent($entity);
		$response->setHttpResponseCode(EnumHttpStatusCode::OK);
        $response->addMessage(10062, EnumMessageLevel::Success, 'message_10062_tag_collection_updated');
		return $response;
    }
}