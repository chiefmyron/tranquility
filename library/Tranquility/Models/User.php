<?php namespace Tranquility\Models;

use \DB                                    as DB;
use \Hash                                  as Hash;
use \Carbon\Carbon;
use \Tranquility\Utility                   as Utility;
use \Tranquility\Enums\System\EntityType   as EnumEntityType;
use \Tranquility\Enums\System\MessageLevel as EnumMessageLevel;

class User extends Entity {	
	
	/**
	 * Create a new User record
	 * IMPORTANT! Does not validate inputs - this should be done by the relevant service wrapper class
	 *
	 * @param array $data The data to be used to create a new User record
	 * @return mixed      The newly created User record (StdObj), or false if errors
	 */
	public function create(array $data) {
		// Set entity type to be User
		$data['type'] = EnumEntityType::User;
		
		// Hash the password string
		$data['password'] = Hash::make($data['password']);
		unset($data['passwordConfirm']);
		
		// Start transaction and create entity record
		DB::beginTransaction();
		$result = parent::create($data);
		
		// Retrieve entity ID and audit trail transaction ID
		$entityId = $result->id;
		$transactionId = $result->transactionId;
		
		// Create User record
		DB::table('tql_entity_users')->insert(array(
			'id'                 => $entityId,
			'username'           => Utility::extractValue($data, 'username', ''),     // Default to empty string
			'password'           => Utility::extractValue($data, 'password'),
			'timezoneCode'       => Utility::extractValue($data, 'timezoneCode'),
			'localeCode'         => Utility::extractValue($data, 'localeCode', ''),   // Default to empty string
			'active'             => Utility::extractValue($data, 'active', ''),
			'securityGroupId'    => Utility::extractValue($data, 'securityGroupId', 0),
			'registeredDateTime' => Utility::extractValue($data, 'registeredDateTime', Carbon::now()),
		));
		
		// Create linkage with parent entity (Person)
		DB::table('tql_entity_xref')->insert(array(
			'parentId' => $data['parentId'],
			'childId' => $entityId
		));
		
		// Create new record for user tokens
		DB::table('tql_sys_user_tokens')->insert(array(
			'userId' => $entityId
		));
		
		// Complete transaction, and return full User record
		DB::commit();
		return $this->find($entityId);
	}
	
	/**
	 * Update an existing User record
	 * IMPORTANT! Does not validate inputs - this should be done by the relevant service wrapper class
	 *
	 * @abstract
	 * @param int   $id    Entity ID of the record to update
	 * @param array $data  Array of data to be updated
	 * @return StdObj      The updated User record (StdObj), or false if errors
	 */
	public function update($id, array $data) {
		// Retrieve the existing User record
		$existing = $this->find($id);
		if ($existing == false) {
			return false;
		}
		
		// Shift current entity record to historical table
		DB::beginTransaction();
		$this->_createHistoricalEntityRecord($id);
		$this->_createHistoricalUserRecord($id);
		
		// Create new audit trail record
		$transactionId = $this->_createTransactionRecord($data);
		
		// Update entity record
		$this->_incrementEntityVersion($id, $transactionId);
		
		// Update User record
		DB::table('tql_entity_users')
			->where('id', '=', $id)
			->update(array(
				'username'        => Utility::extractValue($data, 'username', $existing->username),
				'password'        => Utility::extractValue($data, 'password', $existing->password),
				'timezoneCode'    => Utility::extractValue($data, 'timezoneCode', $existing->timezoneCode),
				'localeCode'      => Utility::extractValue($data, 'localeCode', $existing->localeCode),
				'active'          => Utility::extractValue($data, 'active', $existing->active),
				'securityGroupId' => Utility::extractValue($data, 'securityGroupId', $existing->securityGroupId)				
		));
		
		// Retrieve updated User record (including audit information)
		return $this->find($id);
	}
	
	/**
	 * Logically delete an existing User record
	 * IMPORTANT! Does not validate inputs - this should be done by the relevant service wrapper class
	 *
	 * @abstract
	 * @param  int   $id    Entity ID of the record to delete
	 * @return mixed        The deleted User record (StdObj) if successful, otherwise false 
	 */
	public function delete($id) {
		// Shift current User record to historical table
		DB::beginTransaction();
		$this->_createHistoricalUserRecord($id);
		$user = parent::delete($id);
		
		// Remove record for user tokens
		DB::table('tql_sys_user_tokens')
			->where('userId', '=', $id)
			->delete();
			
		// Commit changes
		DB::commit();
		
		// Retrieve User record (including audit information)
		return $user;
	}
	
	/**
	 * Locate a specific User record
	 *
	 * @param string $searchTerm
	 * @param string $searchField [Optional] Defaults to 'id'
	 * @return mixed Array of data for Person, false if no record found
	 */ 
	public function find($searchTerm, $searchField = 'tql_entity.id') {
		// Define select fields
		$select = array(
			'tql_entity_users.id as id',
			'password',
			'rememberToken',
			'timezoneCode',
			'localeCode',
			'active',
			'securityGroupId',
			'registeredDateTime',
			'tql_entity.transactionId as transactionId',
			'transactionSource',
			'updateBy',
			'updateDateTime',
			'updateReason'			
		);
		
		// Execute query
		$user = DB::table('tql_entity_users')
					->join('tql_entity', 'tql_entity_users.id', '=', 'tql_entity.id')                                   // Join to Entity table
					->join('tql_sys_trans_audit', 'tql_entity.transactionId', '=', 'tql_sys_trans_audit.transactionId') // Join to transaction audit table
					->join('tql_entity_xref', 'tql_entity_users.id', '=', 'tql_entity_xref.childId')                    // Join to Entity cross reference table
					->join('tql_entity_people', 'tql_entity_xref.parentId', '=', 'tql_entity_people.id')                // Join to related Person
					->join('tql_sys_user_tokens', 'tql_entity_users.id', '=', 'tql_sys_user_tokens.userId')             // Join to user tokens 
					->where($searchField, $searchTerm)
					->select($select)
					->first();
		return $user;
	}
	
	public function findByToken($id, $token) {
		// Define select fields
		$select = array(
			'tql_entity_users.id as id',
			'password',
			'rememberToken',
			'timezoneCode',
			'localeCode',
			'active',
			'securityGroupId',
			'registeredDateTime',
			'tql_entity.transactionId as transactionId',
			'transactionSource',
			'updateBy',
			'updateDateTime',
			'updateReason'			
		);
		
		// Execute query
		$user = DB::table('tql_entity_users')
					->join('tql_entity', 'tql_entity_users.id', '=', 'tql_entity.id')                                   // Join to Entity table
					->join('tql_sys_trans_audit', 'tql_entity.transactionId', '=', 'tql_sys_trans_audit.transactionId') // Join to transaction audit table
					->join('tql_entity_xref', 'tql_entity_users.id', '=', 'tql_entity_xref.childId')                    // Join to Entity cross reference table
					->join('tql_entity_people', 'tql_entity_xref.parentId', '=', 'tql_entity_people.id')                // Join to related Person 
					->join('tql_sys_user_tokens', 'tql_entity_users.id', '=', 'tql_sys_user_tokens.userId')             // Join to user tokens
					->where('tql_entity_users.id', '=', $id)                                                            // Limit by user ID
					->where('tql_sys_user_tokens.rememberToken', '=', $token)                                           // Limit by 'remember me' token
					->select($select)
					->first();
		return $user;
	}
	
	public function updateRememberToken($id, $token) {
		DB::table('tql_sys_user_tokens')
			->where('userId', '=', $id)
			->update(array('rememberToken' => $token));
	}
	
	// Retrieve a collection of associated entities
	public function getRelatedEntities($entityId, $filters = array()) {
		throw new \Exception("Not implemented!");
	}
	
	// Retrieve all records of the entity type
	public function get($resultsPerPage = 0, $startRecordIndex = 0, $filterConditions = array(), $orderConditions = array()) {
		// Setup initial query
		$query = DB::table('tql_entity_users')
					->join('tql_entity', 'tql_entity_users.id', '=', 'tql_entity.id')
					->join('tql_sys_trans_audit', 'tql_entity.transactionId', '=', 'tql_sys_trans_audit.transactionId')
					->join('tql_entity_xref', 'tql_entity_users.id', '=', 'tql_entity_xref.childId')
					->join('tql_entity_people', 'tql_entity_xref.parentId', '=', 'tql_entity_people.id');
					
		// Add additional conditions and filters to the query
		$query = $this->_addQueryFilters($query, $resultsPerPage, $startRecordIndex, $filterConditions, $orderConditions);
		
		// Execute query and return results
		$results = $query->get();
		return $results;
	}
	
	// Paginated set of records of the entity type
	public function paginate($perPage = 20, $includeDeleted = false) {
		throw new \Exception("Not implemented!");
	}
	
	/**
	 * Create a copy of the current User record in the User history table for
	 * the specified ID
	 *
	 * @param int $id Entity ID
	 */
	private function _createHistoricalUserRecord($id) {
		// Retrieve existing User record
		$user = $this->find($id);
					
		// Create record in User history table
		DB::table('tql_history_entity_user')->insert(array(
			'id'                 => $user->id,
			'username'           => $user->username,
			'password'           => $user->password,
			'timezoneCode'       => $user->timezoneCode,
			'localeCode'         => $user->localeCode,
			'active'             => $user->active,
			'securityGroupId'    => $user->securityGroupId,
			'registeredDateTime' => $user->registeredDateTime
		));
	}
}