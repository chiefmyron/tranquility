<?php namespace Tranquility\Models;

interface ModelInterface {
	// Retrieve all records of the entity type
	public function get($includeDeleted = false);
	
	// Paginated set of records of the entity type
	public function paginate($perPage = 20, $includeDeleted = false);
	
	// Create a new record for the entity type
	public function create(array $data);
	
	// Update an existing record for the entity type
	public function update($id, array $data);
	
	// Logically delete an existing record for the entity type
	public function delete($id, array $auditTrailDetails);
	
	// Locate a specific record for the entity type 
	public function find($searchTerm, array $searchOptions = array());
}