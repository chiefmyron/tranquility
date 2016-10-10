<?php namespace Tranquility\Services;

// Doctrine 2 libraries
use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Container\Container              as Container;

// Tranquility libraries
use \Tranquility\Utility                        as Utility;
use \Tranquility\Exceptions\ServiceException    as ServiceException;
use \Tranquility\Enums\System\EntityType        as EnumEntityType;
use \Tranquility\Enums\System\MessageLevel      as EnumMessageLevel;
use \Tranquility\Enums\System\HttpStatusCode    as EnumHttpStatusCode;
use \Tranquility\Enums\System\TransactionSource as EnumTransactionSource;

// Base business object
use Tranquility\Data\Objects\BusinessObjects\BusinessObject     as Entity;

class SearchService {
    // Doctrine entity manager
    protected $_entityManager;

	// Set of business objects that can be searched
	protected $_searchableObjects = array(
		EnumEntityType::Person,
		EnumEntityType::Account
	);

	// Set of fields that can be searched on each object
	protected $_searchableFields = array(
		EnumEntityType::Person => ['firstName', 'lastName'],
		EnumEntityType::Account => ['name']
	);
	
	/**
	 * Constructor
	 *
	 * @param \Doctrine\ORM\EntityManagerInterface $em   Doctrine entity manager
	 */
	public function __construct(EntityManagerInterface $em) {
        $this->_entityManager = $em;
	}

	/**
	 * Perform text search on entities
	 * 
	 * @param string $searchTerm
	 * @param array  $filterConditions
	 * @param array  $orderConditions
	 * @param int    $resultsPerPage
	 * @param int    $startRecordIndex
	 * @return \Tranquility\Service\ServiceResponse
	 */
	public function search($searchTerm, $filterConditions = array(), $orderConditions = array(), $resultsPerPage = 0, $startRecordIndex = 0) {
		// Check which entities we are searching on
		$entityTypes = $this->_searchableObjects;
		$entityFilter = Utility::extractValue($filterConditions, 'entities', array());
		if (count($entityFilter) > 0) {
			$entityTypes = array_intersect($entityTypes, $entityFilter);
		}
		unset($filterConditions['entities']);

		// Invoke the text search for each of the searchable entities
		$results = array();
		foreach ($entityTypes as $type) {
			switch($type) {
				case EnumEntityType::Person:

					break;
				case EnumEntityType::Account:
					
					break;
			}
		}
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
        return $this->findBy('id', $id);
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
     * Use the address service to find the parent entity for an address
     *
     * @param int $parentId  ID of the parent entity
     * @return \Tranquility\Services\ServiceResponse
     */
    public function findParentEntity($parentId) {
        $entity = $this->_entityManager->find(Entity::class, $parentId);
		return $this->_findResponse(array($entity));
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
		if (is_null($entities) || $entities === false || count($entities) == 0 || is_null($entities[0])) {
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