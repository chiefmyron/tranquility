<?php namespace Tranquility\Services;

use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Container\Container              as Container;

use \Tranquility\Utility                        as Utility;
use \Tranquility\Exceptions\ServiceException    as ServiceException;
use \Tranquility\Enums\System\MessageLevel      as EnumMessageLevel;
use \Tranquility\Enums\System\HttpStatusCode    as EnumHttpStatusCode;
use \Tranquility\Enums\System\TransactionSource as EnumTransactionSource;

abstract class Service implements \Tranquility\Services\Interfaces\ServiceInterface {
    // Doctrine entity manager
    private $_entityManager;
	
	/**
	 * Constructor
	 *
	 * @param Container $container Laravel IoC container
	 */
	public function __construct(EntityManagerInterface $em) {
        $this->_entityManager = $em;
	}
	
    /**
	 * Specify the name of the business object class
	 *
	 * @abstract
	 * @return string
	 */
	abstract function businessObject(); 
	
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
		
		// Validate that mandatory inputs have been provided
		$mandatoryFieldNames = $this->_getMandatoryFields($newRecord);
		foreach ($mandatoryFieldNames as $field) {
			if (!isset($inputs[$field]) || $inputs[$field] == null || (!is_object($inputs[$field]) && trim($inputs[$field]) == '')) {
				// Mandatory field is missing
				$messages[] = array(
					'code' => 10002,
					'text' => 'message_10002_mandatory_input_field_missing',
					'level' => EnumMessageLevel::Error,
					'fieldId' => $field
				);
			}
		}
		
		// Check that audit trail field 'updateBy' is a valid user
		$updateBy = Utility::extractValue($inputs, 'updateBy', 0);
        if (!($updateBy instanceof \Tranquility\Data\BusinessObjects\UserBusinessObject)) {
            $messages[] = array(
				'code' => 10012,
				'text' => 'message_10012_invalid_user_assigned_to_audit_trail',
				'level' => EnumMessageLevel::Error,
				'fieldId' => 'updateBy'
			);
        }
		
		// Check that audit trail field 'updateDatetime' is a valid date/time value
		if (isset($inputs['updateDateTime']) && preg_match('/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $inputs['updateDateTime']) !== 1) {
			$messages[] = array(
				'code' => 10010,
				'text' => 'message_10010_invalid_datetime_format',
				'level' => EnumMessageLevel::Error,
				'fieldId' => 'updateDatetime'
			);
		}
		
		// Check that audit trail field 'transactionSource' is valid
		if (isset($inputs['transactionSource']) && !EnumTransactionSource::isValidValue($inputs['transactionSource'])) {
			$messages[] = array(
				'code' => 10009,
				'text' => 'message_10009_invalid_transaction_source_code',
				'level' => EnumMessageLevel::Error,
				'fieldId' => 'transactionSource'
			);
		}

		// If there are one or more messages, then there are errors - return messages
		if (count($messages) > 0) {
            // Add top level error message
            $messages[] = array(
				'code' => 10005,
				'text' => 'message_10005_form_validation_errors',
				'level' => EnumMessageLevel::Error,
				'fieldId' => null
            );
			return $messages;
		}
		
		return true;
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
		// If a 'deleted' filter has not been specified, default to select only records that have not been deleted
		$deleted = $this->_checkForFilterCondition($filterConditions, 'deleted');
		if ($deleted === false) {
			$filterConditions[] = array('deleted', '=', 0);
		}
				
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
	 * Find a single entity by ID
	 *
	 * @param int $id Entity ID of the object to retrieve
	 * @return \Tranquility\Services\ServiceResponse
	 */
	public function find($id) {
        $entity = $this->_entityManager->find($this->businessObject(), $id);
		return $this->_findResponse(array($entity));
	}
	
    /**
     * Find a single entity by a specified field
     *
     * @param string $fieldName   Name of the field to search against
     * @param string $fieldValue  Value for entity search
     * @return \Tranquility\Services\ServiceResponse
     */
	public function findBy($fieldName, $fieldValue) {
		// Check field is allowed
		if (!in_array($fieldName, $this->_getFields())) {
			throw new ServiceException('Invalid search field');
		}
        
        $searchOptions = array($fieldName => $fieldValue);
		$entity = $this->_getRepository()->findBy($searchOptions);
		return $this->_findResponse($entity);
	}
	
    /**
     * Create a new record for a business object
     * 
     * @var array $data  Data used to create the new record
     * @return \Tranquility\Services\ServiceResponse
     */
	public function create(array $data) {
		// Set up response object
		$response = new ServiceResponse();
				
		// Perform input validation
		$validation = $this->validateInputFields($data, true);
		if ($validation !== true) {
			// Send error response back immediately
			$response->addMessages($validation);
			$response->setHttpResponseCode(EnumHttpStatusCode::BadRequest);
			return $response;
		}
		
		// Attempt to create the entity
        $entity = $this->_getRepository()->create($data);
		$response->setContent($entity);
		$response->setHttpResponseCode(EnumHttpStatusCode::OK);
		return $response;
	}
	
    /**
     * Update an existing record for the specified business object
     * 
     * @var int   $id    Entity ID for the business object to update
     * @var array $data  New data to update against the existing record
     * @return \Tranquility\Services\ServiceResponse
     */
	public function update($id, array $data) {
		// Set up response object
		$response = new ServiceResponse();
		
		// Perform input validation
		$validation = $this->validateInputFields($data, false);
		if ($validation !== true) {
			// Send error response back immediately
			$response->addMessages($validation);
			$response->setHttpResponseCode(EnumHttpStatusCode::BadRequest);
			return $response;
		}
		
		// Attempt to update the entity
        $entity = $this->_getRepository()->update($id, $data);
		$response->setContent($entity);
		$response->setHttpResponseCode(EnumHttpStatusCode::OK);
		return $response;
	}
	
    /**
     * Mark an existing busines object record as deleted
     * 
     * @var int   $id    Entity ID for the business object to be deleted
     * @var array $data  Audit trail details to be attached to the deleted record
     * @return \Tranquility\Services\ServiceResponse
     */
	public function delete($id, array $data) {
        // Set up response object
		$response = new ServiceResponse();
		
		// Attempt to update the entity
        $entity = $this->_getRepository()->delete($id, $data);
		$response->setContent($entity);
		$response->setHttpResponseCode(EnumHttpStatusCode::OK);
		return $response;
	}
	
    /**
     * Utility function to ensure 'find' functions return results in a uniform format
     *
     * @var mixed $entities  Either a single business object, or a collection of objects
     * @return \Tranquility\Services\ServiceResponse
     */
	protected function _findResponse($entities) {
		$messages = array();
		
		// If no entity was found, set the appropriate error message
		if (is_null($entities) || $entities === false) {
			$messages[] = array(
				'code' => 10001,
				'text' => 'message_10001_record_not_found',
				'level' => EnumMessageLevel::Error
			);
			$entities = array();
		}
		
		// Setup the service response
		$response = new ServiceResponse(array(
			'content' => $entities,
			'messages' => $messages,
			'responseCode' => EnumHttpStatusCode::OK
		));
		return $response;
	}
    
    /**
	 * Get a list of data fields associated with the entity 
	 * @return array
	 */
	protected function _getFields() {
        $className = $this->businessObject();
		return $className::getEntityFields();
	}
	
	/**
	 * Get a list of fields that are mandatory for creating / updating an entity
	 * @return array
	 */
	protected function _getMandatoryFields($newRecord = false) {
		$className = $this->businessObject();
		return $className::getMandatoryEntityFields($newRecord);
	}
    
    /**
     * Return an instance of the repository mapped to the current business object
     * @return \Tranquility\Data\Repositories\Entity
     */
    protected function _getRepository() {
        return $this->_entityManager->getRepository($this->businessObject());
    }
	
    /**
     * Generates a new instance of the business object, and populates it with the
     * supplied data
     *
     * @var array $data  Data to populate the business object
     * @return \Tranquility\Data\BusinessObjects\Entity
     */
    protected function _createBusinessObject($data) {
        // Create business object instance
        $className = $this->businessObject();
        return new $className($data);
    }
    
	/**
	 * Checks for the existence of a particular field in the list of filter conditions
	 *
	 * @param array   $filterConditions The array of filter conditions that will be passed to the model
	 * @param string  $searchTerm       The field name to search for in the list of filter conditions
	 * @return mixed                    If search term is found, return the array containing that filter condition. Otherwise false.
	 */  
	protected function _checkForFilterCondition(array $filterConditions, $searchTerm) {
		foreach ($filterConditions as $filter) {
			if ($filter[0] == $searchTerm) {
				return $filter;
			}
		}
		
		return false;
	}	
}