<?php namespace Tranquility\Services;

use \Hash;
use \Tranquility\Utility                      as Utility;
use \Tranquility\Enums\System\MessageLevel    as EnumMessageLevel;
use \Tranquility\Enums\System\HttpStatusCode  as EnumHttpStatusCode;

class UserService extends \Tranquility\Services\Service {
    /**
     * Specify actual business object class name
     *
     * @return string
     */
    public function businessObject() {
        return 'Tranquility\Data\Objects\BusinessObjects\UserBusinessObject';
    }
    
    /**
	 * Create a new User record
	 *
	 * @param array Data for creating a new User record
	 * @return \Tranquility\Services\ServiceResponse
	 */
	public function create(array $data) {
        // Set up response object
		$response = new ServiceResponse();
                
        // Set any empty strings to nulls
        foreach ($data as $key => $value) {
            if ($value === '') {
                $data[$key] = null;
            }
        }
				
		// Perform input validation
		$validation = $this->validateInputFields($data, true);
		if ($validation !== true) {
			// Send error response back immediately
			$response->addMessages($validation);
			$response->setHttpResponseCode(EnumHttpStatusCode::BadRequest);
			return $response;
		}
        
        // Encode password
        $data['password'] = Hash::make($data['password']); 
        unset($data['passwordConfirm']);
        
        // Retrieve parent entity
        $parentId = Utility::extractValue($data, 'parentId', 0);
        $response = $this->findParentEntity($parentId);
        if ($response->containsErrors()) {
            return $response;
        }
        $data['parent'] = $response->getFirstContentItem();
		
		// Attempt to create the user
        $user = $this->_getRepository()->create($data);
		$response->setContent($user);
		$response->setHttpResponseCode(EnumHttpStatusCode::OK);
        $response->addMessage(10030, EnumMessageLevel::Success, 'message_10030_user_record_created_successfully', ['name' => $data['parent']->getFullName()]);
		return $response;
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
     * Changes the password for an existing user record
     *
     * @param int   $id    ID for existing User record
     * @param array $data  Data for updating the User record with new password
     * @return \Tranquility\Services\ServiceResponse
     */
    public function changePassword($id, array $data) {
        // Set up response object
		$response = new ServiceResponse();
		
		// Perform input validation
        $auditTrailMessages = $this->validateAuditTrailFields($data);
        $passwordMessages = $this->validateNewPasswordFields($data);
        $messages = array_merge($auditTrailMessages, $passwordMessages);
		if (count($messages) > 0) {
            // Add top level error message
            $messages[] = array(
				'code' => 10005,
				'text' => 'message_10005_form_validation_errors',
				'level' => EnumMessageLevel::Error,
				'fieldId' => null
            );
            
			// Send error response back immediately
			$response->addMessages($messages);
			$response->setHttpResponseCode(EnumHttpStatusCode::BadRequest);
			return $response;
		}
        
        // Encode password
        $data['password'] = Hash::make($data['password']); 
        unset($data['passwordConfirm']);
		
		// Attempt to update the entity
        $user = $this->_getRepository()->update($id, $data);
		$response->setContent($user);
        $response->addMessage(10036, EnumMessageLevel::Success, 'message_10036_password_updated_successfully', ['name' => $user->getDisplayName()]);
		$response->setHttpResponseCode(EnumHttpStatusCode::OK);
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
            $messages = array_merge($messages, $this->validateNewPasswordFields($inputs));
		}
        
        // Username validation
		if (isset($inputs['username']) && !filter_var($inputs['username'], FILTER_VALIDATE_EMAIL)) {
			$messages[] = array(
				'code' => 10004,
				'text' => 'message_10004_username_must_be_email_address',
				'level' => EnumMessageLevel::Error,
				'fieldId' => 'username'
			);
            $messages[] = array(
				'code' => 10004,
				'text' => 'message_10004_username_must_be_email_address',
				'level' => EnumMessageLevel::Error,
				'fieldId' => 'newUsername'
			);
		}
		
		// If there are one or more messages, then there are errors - return messages
		if (count($messages) > 0) {
			return $messages;
		}
		
		return true;
	}
    
    /**
	 * Validate data for audit trail fields only 
	 * 
	 * @param array   $inputs    Array of data field values
	 * @return array  Error messages from validation. Empty array if no errors.
	 */
    public function validateNewPasswordFields($inputs) {
        $messages = array();
        $password = Utility::extractValue($inputs, 'password', '');
        $passwordConfirm = Utility::extractValue($inputs, 'passwordConfirm', '');
        
        // Check that passwords match
        if ($password != $passwordConfirm) {
            $messages[] = array(
				'code' => 10003,
				'text' => 'message_10003_passwords_must_match',
				'level' => EnumMessageLevel::Error,
				'fieldId' => 'password'
			);
        }
        
        // Check password meets minimum length requirements
        if (strlen($password) < config('tranquility.minimum_password_length')) {
            $messages[] = array(
				'code' => 10035,
				'text' => 'message_10035_password_not_long_enough',
                'params' => array('length' => config('tranquility.minimum_password_length')),
				'level' => EnumMessageLevel::Error,
				'fieldId' => 'password'
			);
        }
        
        // TODO: Password complexity rule
        
        
        return $messages;
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
		return $this->_findResponse(array($entity));
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