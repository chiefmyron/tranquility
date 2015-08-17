<?php namespace Tranquility\Services;

use Illuminate\Container\Container              as Container;
use \Tranquility\Utility                        as Utility;
use \Tranquility\Exception                      as Exception;
use \Tranquility\Enums\System\MessageLevel      as EnumMessageLevel;
use \Tranquility\Enums\System\HttpStatusCode    as EnumHttpStatusCode;
use \Tranquility\Enums\System\TransactionSource as EnumTransactionSource;

abstract class Service implements \Tranquility\Services\Interfaces\ServiceInterface {
	// Laravel IoC container
	private $container;
	
	// Model used for data access layer
	protected $model;
	
	// Common fields for all entities
	protected $_commonFields = array(
		'id',
		'version',
		'type',
		'subType',
		'deleted',
		'transactionId'	
	);
	
	// Mandatory fields when creating or updating an entity
	protected $_commonMandatoryFields = array(
		'type',
		'transactionSource',
		'updateBy',
		'updateDatetime',
		'updateReason'
	);

	// Audit trail fields
	protected $_auditFields = array(
		'transactionSource',
		'updateBy',
		'updateDateTime',
		'updateReason'	
	);
	
	/**
	 * Constructor
	 *
	 * @param Container $container Laravel IoC container
	 */
	public function __construct(Container $container) {
		$this->container = $container;
		$this->makeModel();
	}
	
	/**
	 * Specify the name of the model class
	 *
	 * @abstract
	 * @return mixed
	 */
	abstract function model(); 
	
	/**
	 * Instantiate model class to allow data access
	 *
	 * @return \Tranquility\Model
	 */
	public function makeModel() {
		$model = $this->container->make($this->model());
		
		if (!$model instanceof \Tranquility\Models\Entity) {
			throw new Exception("Class ".$this->model()." must be an instance of Tranquility\\Models\\Entity");
		}
		
		$this->model = $model;
		return $this->model;
	}
	
	/**
	 * Get a list of data fields associated with the entity 
	 * @return array
	 */
	protected function _getFields() {
		return array_merge($this->_commonFields, $this->_auditFields);
	}
	
	/**
	 * Get a list of fields that are mandatory for creating / updating an entity
	 * @return array
	 */
	protected function _getMandatoryFields($newRecord = false) {
		// If updating an existing record, the field 'id' is also mandatory
		if (!$newRecord) {
			return array_merge(array('id'), $this->_commonMandatoryFields);
		}
		
		return $this->_commonMandatoryFields;
	}
	
	/**
	 * Gets a list of fields that will always be present in the audit trail
	 * 
	 * @return array
	 */
	protected function _getAuditTrailFields() {
		return $this->_auditFields;
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
		
		// Validate that mandatory inputs have been provided
		$mandatoryFieldNames = $this->_getMandatoryFields($newRecord);
		foreach ($mandatoryFieldNames as $field) {
			if (!isset($inputs[$field]) || $inputs[$field] == null || trim($inputs[$field]) == '') {
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
		$updateBy = Utility::extractValue($inputs, 'updateBy', 0, 'int');
		// TODO: Entity check
		
		// Check that audit trail field 'updateDatetime' is a valid date/time value
		if (isset($inputs['updateDatetime']) && preg_match('/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $inputs['updateDatetime']) !== 1) {
			$messages[] = array(
				'code' => 10008,
				'text' => 'message_10008_invalid_datetime_format',
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
	public function all($resultsPerPage = 0, $startRecordIndex = 0, $filterConditions = array(), $orderConditions = array()) {
		// If a 'deleted' filter has not been specified, default to select only records that have not been deleted
		$deleted = $this->_checkForFilterCondition($filterConditions, 'deleted');
		if ($deleted === false) {
			$filterConditions[] = array('deleted', '=', 0);
		}
				
		$results = $this->model->get($resultsPerPage, $startRecordIndex, $filterConditions, $orderConditions);
		
		// If no results are returned, add a warning message to the response
		$messages = array();
		if (count($results) <= 0) {
			$messages[] = array(
				'code' => 10000,
				'text' => 'message_10000_no_records_returned',
				'level' => EnumMessageLevel::Warning
			);
		}
		
		// Set up the response message
		$response = new ServiceResponse(array(
			'content' => $results,
			'messages' => $messages,
			'responseCode' => EnumHttpStatusCode::OK
		));
		return $response;
	}
	
	public function paginate($perPage = 20) {
		return $this->model->paginate($perPage, $columns);
	}
	
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
		$results = $this->model->create($data);
		$response->setContent(array($results));
		$response->setHttpResponseCode(EnumHttpStatusCode::OK);
		return $response;
	}
	
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
		$results = $this->model->update($id, $data);
		$response->setContent(array($results));
		$response->setHttpResponseCode(EnumHttpStatusCode::OK);
		return $response;
	}
	
	public function delete($id) {
		return $this->model->delete($id, $data);
	}
	
	/**
	 * Find a single entity by ID
	 *
	 * @param int $id Entity ID of the object to retrieve
	 * @return \Tranquility\Services\ServiceResponse
	 */
	public function find($id) {
		$entity = $this->model->find($id);
		return $this->_findResponse($entity);
	}
	
	public function findBy($fieldName, $fieldValue) {
		// Check field is allowed
		if (!in_array($fieldName, $this->_getFields())) {
			throw new Exception('Invalid search field');
		}
		
		$entity = $this->model->find($fieldValue, $fieldName);
		return $this->_findResponse($entity);
	}
	
	protected function _findResponse($entity) {
		$messages = array();
		
		// If no entity was found, set the appropriate error message
		if (is_null($entity) || $entity === false) {
			$messages[] = array(
				'code' => 10001,
				'text' => 'message_10001_record_not_found',
				'level' => EnumMessageLevel::Error
			);
			$entity = array();
		}
		
		// Setup the service response
		$response = new ServiceResponse(array(
			'content' => array($entity),
			'messages' => $messages,
			'responseCode' => EnumHttpStatusCode::OK
		));
		return $response;
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