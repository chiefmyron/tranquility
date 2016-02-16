<?php namespace Tranquility\Services;

use \Log;
use \Tranquility\Utility                     as Utility;
use \Tranquility\Enums\System\EntityType     as EnumEntityType;
use \Tranquility\Enums\System\MessageLevel   as EnumMessageLevel;
use \Tranquility\Enums\System\HttpStatusCode as EnumHttpStatusCode;
use \Tranquility\Exceptions\ServiceException as Exception;

class AddressService extends \Tranquility\Services\Service {
    /**
     * Specify business object name
     *
     * @return string
     */
    public function businessObject() {
        return 'Tranquility\Data\BusinessObjects\AddressPhysicalBusinessObject';
    }
	
	/**
	 * Create a new Address record
	 *
	 * @param array Data for creating a new Address record
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
        
        // Perform geolocation for physical addresses
        if ($data['type'] == EnumEntityType::AddressPhysical) {
            $classname = $this->businessObject();
            $address = new $classname($data);
            $coordinates = $this->_callGeolocationService($address);
            $data['latitude'] = $coordinates['latitude'];
            $data['longitude'] = $coordinates['longitude'];
        }
		
		// Attempt to create the entity
        $entity = $this->_getRepository()->create($data);
		$response->setContent($entity);
		$response->setHttpResponseCode(EnumHttpStatusCode::OK);
		
		// Add entity specific success code
		if (!$response->containsErrors()) {
			$response->addMessage(10040, EnumMessageLevel::Success, 'message_10040_physical_address_record_created_successfully');
		}
		
		return $response;
	}
	
	/**
	 * Updates an existing Address record
	 *
	 * @param int   $id    ID for existing Address record
	 * @param array $data  Data for updating an existing Address record
	 * @return \Tranquility\Services\ServiceResponse
	 */
	public function update($id, array $data) {
		$response = parent::update($id, $data);
		
		// Add entity specific success code
		if (!$response->containsErrors()) {
			$response->addMessage(10021, EnumMessageLevel::Success, 'message_10021_person_record_updated_successfully');
		}
		
		return $response;
	}
    
    /** Deletes an existing Address record
     *
     * @param int   $id                ID for existing Address record
     * @param array $auditTrailFields  Array containing audit trail information
     * @return \Tranquility\Services\ServiceResponse
     */
    public function delete($id, array $auditTrailFields) {
		// Attempt to update the entity
        $response = parent::delete($id, $auditTrailFields);
        
        // Add entity specific success code
        if (!$response->containsErrors()) {
			$response->addMessage(10022, EnumMessageLevel::Success, 'message_10022_person_record_deleted_successfully');
		}
        
		return $response;
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
		
		// Perform mandatory field and audit trail field validation
		$result = parent::validateInputFields($inputs, $newRecord);
		if ($result !== true) {
			$messages = $result;
		}
		
		// TODO: Validate title code against reference data table
		
		// If there are one or more messages, then there are errors - return messages
		if (count($messages) > 0) {
			return $messages;
		}
		
		return true;
	}
    
    /**
     * Performs geolocation of an address string. Content property of response
     * object will contain an array of geolocation data.
     * 
     * @param string $address Address to perform geolocation on
     * @return \Tranquility_ServiceResponse
     */
    public function performGeolocation($address) {
        
    }
    
    protected function _callGeolocationService($address) {
        $classname = $this->businessObject();
        if (!($address instanceof $classname)) {
            throw new Exception('Geolocation is only for physical address records!');
        }
        
        // Check config to see if we should make an external call
        if (config('tranquility.geolocation_enabled') == false) {
            return array('latitute' => 0, 'longitude' => 0);
        }
        
        // Setup full URL to use for geolocation service
        $uri = config('tranquility.geolocation_service_uri').'json?sensor=false&address='.utf8_encode($address->urlEncodedAddress());
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $uri);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // Execute call
        $data = curl_exec($ch);
        if ($data == false) {
            // Error has occured in cURL call
            Log::warning('Call to external geolocation serivce failed - '.curl_error($ch).' (Error number: '.curl_errno($ch).')');
            return false;
        }
        
        // Successful call
        curl_close($ch);
        $geolocationData = json_decode($data, true);
        
        // Check if the response is in a recognised format
        if (!is_array($geolocationData) || !isset($geolocationData['status'])) {
            // Response is not in a recognised format
            Log::warning('Call to external geolocation was successful, but response was not in the expected format. Response text was: '.$geolocationData);
            return false;
        }
        
        // Check the result code
        if ($geolocationData['status'] != 'OK') {
            switch ($geolocationData['status']) {
                case 'ZERO_RESULTS':
                    // Successful service call, but no results
                    Log::info('Call to external geolocation was successful, but no results were found.');
                case 'OVER_QUERY_LIMIT':
                    // Over the quota of geolocation calls allowed
                    Log::warning('External service quota exceeded');
                case 'REQUEST_DENIED':
                case 'INVALID_REQUEST':
                default:
                    // Incorrectly formed URL (due to lack of explicit 'sensor' parameter) or 'address' or 'latlng' parameters missing
                    Log::warning('Request to external geolocation service was badly formed.');
            }
            return array('latitude' => 0, 'longitude' => 0);            
        }
        
        // We were successful, so return an array with latitude and longitude
        $coordinates = array(
            'latitude' => $geolocationData['results'][0]['geometry']['location']['lat'],
            'longitude' => $geolocationData['results'][0]['geometry']['location']['lng']
        );
        return $coordinates;
    }
}	