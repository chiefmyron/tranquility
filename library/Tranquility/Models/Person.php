<?php namespace Tranquility\Models;

use \DB                                    as DB;
use \Tranquility\Utility                   as Utility;
use \Tranquility\Models\ModelException     as ModelException;
use \Tranquility\Enums\System\EntityType   as EnumEntityType;
use \Tranquility\Enums\System\MessageLevel as EnumMessageLevel;

class Person extends Entity {	
	
	/**
	 * Create a new Person record
	 * IMPORTANT! Does not validate inputs - this should be done by the relevant service wrapper class
	 *
	 * @param array $data The data to be used to create a new Person record
	 * @return mixed      The newly created Person record (StdObj), or false if errors
	 */
	public function create(array $data) {
		// Set entity type to be Person
		$data['type'] = EnumEntityType::Person;
		
		// Start transaction and create entity record
		DB::beginTransaction();
		$result = parent::create($data);
		
		// Retrieve entity ID and audit trail transaction ID
		$entityId = $result->id;
		$transactionId = $result->transactionId;
		
		// Create Person record
		DB::table('tql_entity_people')->insert(array(
			'id'        => $entityId,
			'title'     => Utility::extractValue($data, 'title', ''),     // Default to empty string
			'firstName' => Utility::extractValue($data, 'firstName'),
			'lastName'  => Utility::extractValue($data, 'lastName'),
			'position'  => Utility::extractValue($data, 'position', '')   // Default to empty string
		));
		
		// Complete transaction, and return full Person record
		DB::commit();
		return $this->find($entityId);
	}
	
	/**
	 * Update an existing Person record
	 * IMPORTANT! Does not validate inputs - this should be done by the relevant service wrapper class
	 *
	 * @abstract
	 * @param int   $id    Entity ID of the record to update
	 * @param array $data  Array of data to be updated
	 * @return StdObj      The updated Person record (StdObj), or false if errors
	 */
	public function update($id, array $data) {
		// Retrieve the existing Person record
		$existing = $this->find($id);
		if ($existing == false) {
			return false;
		}
		
		// Shift current entity record to historical table
		DB::beginTransaction();
		$this->_createHistoricalEntityRecord($id);
		$this->_createHistoricalPersonRecord($id);
		
		// Create new audit trail record
		$transactionId = $this->_createTransactionRecord($data);
		
		// Update entity record
		$this->_incrementEntityVersion($id, $transactionId);
		
		// Update Person record
		DB::table('tql_entity_people')
			->where('id', '=', $id)
			->update(array(
				'title'     => Utility::extractValue($data, 'title', $existing->title),
				'firstName' => Utility::extractValue($data, 'firstName', $existing->firstName),
				'lastName'  => Utility::extractValue($data, 'lastName', $existing->lastName),
				'position'  => Utility::extractValue($data, 'position', $existing->position)
		));
		DB::commit();
		
		// Retrieve updated Person record (including audit information)
		return $this->find($id);
	}
	
	/**
	 * Logically delete an existing Person record
	 * IMPORTANT! Does not validate inputs - this should be done by the relevant service wrapper class
	 *
	 * @abstract
	 * @param int   $id    Entity ID of the record to delete
	 * @return void 
	 */
	public function delete($id, array $auditTrailDetails) {
        // Start transaction
        DB::beginTransaction();
        
        // Create historical record of person, and then mark entity as deleted
        $this->_createHistoricalPersonRecord($id);
        parent::delete($id, $auditTrailDetails);
        DB::commit();
	}
	
	/**
	 * Locate a specific Person record
	 *
	 * @param string $searchTerm
	 * @param array  $searchOptions Array of key value pairs used to further restrict the search paramters
	 * @return mixed Array of data for Person, false if no record found
	 */ 
	public function find($searchTerm, array $searchOptions = array()) {
        // Extract search options
        $searchField = Utility::extractValue($searchOptions, 'searchField', 'tql_entity.id');
        $includeDeleted = Utility::extractValue($searchOptions, 'includeDeleted', false, 'boolean');
        
		// Execute query
        $query = DB::table('tql_entity_people')
					->join('tql_entity', 'tql_entity_people.id', '=', 'tql_entity.id')
					->join('tql_sys_trans_audit', 'tql_entity.transactionId', '=', 'tql_sys_trans_audit.transactionId')
					->where($searchField, $searchTerm);
        
        // If we are not including deleted entities, add an additional filter
        if (!$includeDeleted) {
            $query = $query->where('tql_entity.deleted', 0);
        }
		
        // Return record
		return $query->first();
	}
	
	// Retrieve a collection of associated entities
	public function getRelatedEntities($entityId, $filters = array()) {
		throw new ModelException("Not implemented!");
	}
	
	// Retrieve all records of the entity type
	public function get($resultsPerPage = 0, $startRecordIndex = 0, $filterConditions = array(), $orderConditions = array()) {
		// Setup initial query
		$query = DB::table('tql_entity_people')
					->join('tql_entity', 'tql_entity_people.id', '=', 'tql_entity.id')
					->join('tql_sys_trans_audit', 'tql_entity.transactionId', '=', 'tql_sys_trans_audit.transactionId');
					
		// Add additional conditions and filters to the query
		$query = $this->_addQueryFilters($query, $resultsPerPage, $startRecordIndex, $filterConditions, $orderConditions);
		
		// Execute query and return results
		$results = $query->get();
		return $results;
	}
	
	// Paginated set of records of the entity type
	public function paginate($perPage = 20, $includeDeleted = false) {
		throw new ModelException("Not implemented!");
	}
	
	/**
	 * Create a copy of the current Person record in the Person history table for
	 * the specified ID
	 *
	 * @param int $id Entity ID
	 */
	private function _createHistoricalPersonRecord($id) {
		// Retrieve existing person record
		$person = $this->find($id);
					
		// Create record in person history table
		DB::table('tql_history_entity_people')->insert(array(
			'id'        => $person->id,
			'version'   => $person->version,
			'title'     => $person->title,
			'firstName' => $person->firstName,
			'lastName'  => $person->lastName,
			'position'  => $person->position
		));
	}
}