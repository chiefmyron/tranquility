<?php namespace Tranquility\Models;

use \DB                                    as DB;
use \Tranquility\Utility                   as Utility;
use \Tranquility\Models\ModelException     as ModelException;
use \Tranquility\Enums\System\EntityType   as EnumEntityType;
use \Tranquility\Enums\System\MessageLevel as EnumMessageLevel;

abstract class Entity implements ModelInterface {	
	/**
	 * Create a new entity record
	 * IMPORTANT! Does not validate inputs - this should be done by the relevant Repository class
	 *
	 * @param array $data
	 * @param array 
	 */
	public function create(array $data) {
		// Retrieve entity type from inputs
		$type = Utility::extractValue($data, 'type');
		if (!EnumEntityType::isValidValue($type)) {
			throw new ModelException('Unknown entity type supplied while trying to create a new entity record: '.$type);
		}
		
		// Create new audit trail record
		DB::beginTransaction();
		$transactionId = $this->_createTransactionRecord($data);
		
		// Create entity record
		$id = DB::table('tql_entity')->insertGetId(array(
			'version' => 1,
			'type' => $type,
			'subType' => Utility::extractValue($data, 'subType'),
			'deleted' => 0,
			'transactionId' => $transactionId
		));
		DB::commit();
		
		// Retrieve entity record (including audit information)
		return $this->_retrieveEntityRecord($id);
	}
	
	/**
	 * Update an existing entity record
	 * IMPORTANT! Does not validate inputs - this should be done by the relevant Repository class
	 *
	 * @param int   $id    Entity ID of the record to update
	 * @param array $data
	 * @param array 
	 */
	public function update($id, array $data) {
		// Shift current entity record to historical table
		DB::beginTransaction();
		$this->_createHistoricalEntityRecord($id);
		
		// Create new audit trail record
		$transactionId = $this->_createTransactionRecord($data);
		
		// Update entity record
		DB::table('tql_entity')
			->where('id', '=', $id)
			->increment('version', 1, array(
				'type' => Utility::extractValue($data, 'type'),
				'subType' => Utility::extractValue($data, 'subType'),
				'deleted' => Utility::extractValue($data, 'deleted'),
				'transactionId' => $transactionId
		));
		DB::commit();
		
		// Retrieve entity record (including audit information)
		return $this->_retrieveEntityRecord($id);
	}
	
	/**
	 * Logically delete an existing entity record
	 * IMPORTANT! Does not validate inputs - this should be done by the relevant service wrapper class
	 *
	 * @param int   $id    Entity ID of the record to delete
	 * @param array 
	 */
	public function delete($id) {
	    // Shift current entity record to historical table
		DB::beginTransaction();
		$this->_createHistoricalEntityRecord($id);
		
		// Create new audit trail record, and mark record as deleted
		$transactionId = $this->_createTransactionRecord($data);
		DB::table('tql_entity')
			->where('id', '=', $id)
			->increment('version', 1, array(
				'deleted' => 1,
				'transactionId' => $transactionId
		));
		DB::commit();
		
		// Retrieve entity record (including audit information)
		return $this->find($id);
	}
	
	// Locate a specific record for the entity type 
	abstract function find($searchTerm, $searchField = 'tql_entity.id');
	
	// Retrieve a collection of associated entities
	abstract function getRelatedEntities($entityId, $filters = array());
	
	// Retrieve all records of the entity type
	abstract function get($includeDeleted = false);
	
	// Paginated set of records of the entity type
	abstract function paginate($perPage = 20, $includeDeleted = false);
	
	/**
	 * Create a new audit trail record with a unique transaction ID
	 *
	 * The transaction record will contain all of the audit trail information
	 * and should be associated with any eneity being updated.
	 *
	 * @param array $data Audit trail inputs (transactionSource, updateBy, updateDatetime, updateReason)
	 * @return int The auto-generated transactionID
	 * @throws \Tranquility\Models\ModelException
	 */
	protected function _createTransactionRecord(array $data) {
		// Create new audit trail record
		$id = DB::table('tql_sys_trans_audit')->insertGetId(array(
			'transactionSource' => Utility::extractValue($data, 'transactionSource'),
			'updateBy'          => Utility::extractValue($data, 'updateBy'),
			'updateDatetime'    => Utility::extractValue($data, 'updateDatetime'),
			'updateReason'      => Utility::extractValue($data, 'updateReason')
		));
		
		// Check that transaction ID is valid
		if ($id <= 0) {
			throw new ModelException('Unable to create new audit trail record');
		}
		
		// Return newly generated transaction ID
		return $id;
	}
	
	/**
	 * Increments the 'version' counter on an entity record by one,
	 * and updates with a new transaction ID
	 *
	 * @param int Entity ID
	 * @param int Transaction ID
	 * @return void
	 */
	protected function _incrementEntityVersion($id, $transactionId) {
		DB::beginTransaction();
		DB::table('tql_entity')
			->where('id', '=', $id)
			->increment('version', 1, array('transactionId' => $transactionId));
		DB::commit();
	}
	
	/**
	 * Marks an entity record as logically deleted and updates with a new 
	 * transaction ID
	 *
	 * @param int $id            Entity ID
	 * @param int $transactionId Transaction identifier
	 * @return void
	 */
	protected function _deleteEntityRecord($id, $transactionId) {
		DB::beginTransaction();
		
		DB::commit();
	}
	
	/**
	 * Retrieve entity record (including associated audit trail details)
	 *
	 * @param int $id Entity ID
	 * @retun array
	 */
	private function _retrieveEntityRecord($id) {
		$result = DB::table('tql_entity')
					->join('tql_sys_trans_audit', 'tql_entity.transactionId', '=', 'tql_sys_trans_audit.transactionId')
					->where('tql_entity.id', '=', $id)
					->first();
		return $result;
	}
	
	/**
	 * Create a copy of the current entity record in the entity history table for
	 * the specified ID
	 *
	 * @param int $id Entity ID
	 */
	protected function _createHistoricalEntityRecord($id) {
		// Retrieve existing entity record
		$entity = DB::table('tql_entity')
					->where('id', '=', $id)
					->first();
					
		// Create record in entity history table
		DB::table('tql_history_entity')->insert(array(
			'id'            => $entity->id,
			'version'       => $entity->version,
			'type'          => $entity->type,
			'subType'       => $entity->subType,
			'deleted'       => $entity->deleted,
			'transactionId' => $entity->transactionId
		));
	}
	
	/**
	 * Used to add additional query conditions, ordering and set limits to a selection query
	 *
	 * @param \Illuminate\Database\Query\Builder $query   The initial selection query
	 * @param int    $resultsPerPage                      The number of records to select. If set to 0, will return the entire set
	 * @param int    $startRecordIndex                    The index of the record to start selection from
	 * @param array  $filterConditions 
	 * @param array  $orderConditions
	 * @return \Illuminate\Database\Query\Builder
	 */
	protected function _addQueryFilters($query, $resultsPerPage = 0, $startRecordIndex = 0, $filterConditions = array(), $orderConditions = array()) {
		// Add filter conditions
		foreach ($filterConditions as $filter) {
			$query = $query->where($filter[0], $filter[1], $filter[2]);
		}
		
		// Add order statements
		foreach ($orderConditions as $order) {
			$query = $query->orderBy($order[0], $order[1]);
		} 
		
		// Add offset statement, if required
		if ($resultsPerPage > 0) {
			$query = $query->skip($startRecordIndex)->take($resultsPerPage);
		}
		return $query;
	}
}