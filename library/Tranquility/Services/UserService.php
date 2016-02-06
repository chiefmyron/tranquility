<?php namespace Tranquility\Services;

use \Tranquility\Enums\System\MessageLevel    as EnumMessageLevel;
use \Tranquility\Enums\System\HttpStatusCode  as EnumHttpStatusCode;

class UserService extends \Tranquility\Services\Service {
    /**
     * Specify actual business object class name
     *
     * @return string
     */
    public function businessObject() {
        return 'Tranquility\Data\BusinessObjects\UserBusinessObject';
    }
    
    /**
	 * Updates an existing User record
	 *
	 * @param int   $id    ID for existing User record
	 * @param array $data  Data for updating an existing User record
	 * @return \Tranquility\Services\ServiceResponse
	 */
	public function update($id, array $data) {
        // Strip out password - must be updated via separate operation
        unset($data['password']);
		$response = parent::update($id, $data);
		
		// Add entity specific success code
		if (!$response->containsErrors()) {
            $user = $response->getFirstContentItem();
			$response->addMessage(10031, EnumMessageLevel::Success, 'message_10031_user_record_updated_successfully', ['name' => $user->getDisplayName()]);
		}
		
		return $response;
	}
	
	/**
	 * Validate data for input fields - this includes checking mandatory fields and audit
	 * trail fields
	 * 
	 * @param array   $inputs     Array of data field values
	 * @param boolean $newRecord  True if creating validating fields for a new record
	 * @return mixed  True if valid input, array of messages if invalid input
	 */
	public function validateInputFields($inputs, $newRecord = false) {
		$messages = array();
		
		// Perform mandatory field and audit trail field validation
		$result = parent::validateInputFields($inputs, $newRecord);
		if ($result !== true) {
			$messages = $result;
		}
		
		// Password verification
		if ($newRecord) {
			// Check password and password confirmation match
			if ($inputs['password'] != $inputs['passwordConfirm']) {
				// Passwords must match
				$messages[] = array(
					'code' => 10003,
					'text' => 'message_10003_passwords_must_match',
					'level' => EnumMessageLevel::Error,
					'fieldId' => 'passwordConfirm'
				);
			}
			
			// Check password minimum length
			
		}
		
		// If there are one or more messages, then there are errors - return messages
		if (count($messages) > 0) {
			return $messages;
		}
		
		return true;
	}
	
    /**
	 * Retrieve a user by by their unique identifier and "remember me" token.
	 *
	 * @param  mixed   $id
	 * @param  string  $token
	 * @return \Tranquility\Services\ServiceResponse
	 */
	public function findByToken($id, $token) {
        $entity = $this->_getRepository()->findByToken($id, $token);
		return $this->_findResponse($entity);
	}
	
    /**
     * Update the "remember me" token for the given user in storage.
     *
     * @param  int     $id
     * @param  string  $token
     * @return \Tranquility\Services\ServiceResponse
     */
	public function updateRememberToken($id, $token) {
        // Set up response object
		$response = new ServiceResponse();
        $this->_getRepository()->updateRememberToken($id, $token);
		$response->setHttpResponseCode(EnumHttpStatusCode::OK);
		return $response;
	}
    
    /**
	 * Retrieve all users that have Person records associated with them
	 *
	 * @param array $filter Used to specify additional filters to the set of results
	 * @param array $order Used to specify order parameters to the set of results
     * @param int $resultsPerPage If zero or less, or null, the full result set will be returned
	 * @param int $startRecordIndex Index of the record to start the result set from. Defaults to zero.
	 * @return \Tranquility\Service\ServiceResponse
	 */
    public function getPeopleWithUserAccounts($filterConditions = array(), $orderConditions = array(), $resultsPerPage = 0, $startRecordIndex = 0) {
        // If a 'deleted' filter has not been specified, default to select only records that have not been deleted
		$deleted = $this->_checkForFilterCondition($filterConditions, 'deleted');
		if ($deleted === false) {
			$filterConditions[] = array('deleted', '=', 0);
		}
				
        // Convert result set into array of business objects
        $businessObjects = $this->_getRepository()->getPeopleWithUserAccounts($filterConditions, $orderConditions, $resultsPerPage, $startRecordIndex);
		
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
}