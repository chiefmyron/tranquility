<?php namespace Tranquility\Services;

/**
 * Representation of a service response message. Can be used internally via
 * an application, or externally via a REST API.
 *
 * @package Tranquility\Services
 * @author  Andrew Patterson <patto@live.com.au>
 */

use \Tranquility\Utility                     as Utility;
use \Tranquility\Enums\System\EntityType     as EnumEntityType;
use \Tranquility\Enums\System\MessageLevel   as EnumMessageLevel;
use \Tranquility\Enums\System\HttpStatusCode as EnumHttpStatusCode;
use \Tranquility\Exceptions\ServiceException as ServiceException;

class ServiceResponse {
	/**
	 * Main content to be returned in the response
	 * @var array
	 */
	protected $_content = array();
	
	/**
	 * Any messages to be returned in the response.
	 *
	 * Each message should be in an individual array, containing keys for code,
	 * level, error text and (optionally) the associated field ID.
	 * @var array
	 */	 
	protected $_messages = array();
	
	/** 
	 * Response metadata - record count, audit trail information, etc
	 * @var array
	 */
	protected $_meta = array();
	
	/**
	 * Unique transaction ID generated on any create / update / delete action
	 * @var int
	 */
	protected $_transactionId = 0;
	
	/**
	 * HTTP response code
	 * @var int
	 */
	protected $_httpResponseCode = 0;
	
	/** 
	 * Constructor
	 *
	 * @param array $options Valid keys are 'content', 'messages', 'meta' and 'responseCode'
	 * @return void
	 */
	public function __construct(array $options = array()) {
		// Set message content, if supplied
		$content = Utility::extractValue($options, 'content', array());
		$this->setContent($content, false);
		
		// Set initial messages, if supplied
		if (isset($options['messages']) && is_array($options['messages'])) {
			$this->addMessages($options['messages']);
		}
		
		// Initialise / calculate metadata attributes
		$this->calculateMetadata();
		$metadata = Utility::extractValue($options, 'meta', array());
		$this->setMetadata($metadata);
		
		// Set HTTP response code, if supplied
		if (isset($options['responseCode'])) {
			$this->setHttpResponseCode($options['responseCode']);
		}
	}
	
	/**
	 * Sets the main content block(s) that will be returned in the service response
	 *
	 * @param mixed $content One or more content blocks to be returned
	 * @param boolean $calculateMetadata If true, metadata for the response will automatically be calculated
	 * @return void
	 * @throws \Tranquility\Services\ServiceException
	 */
	public function setContent($content = array(), $calculateMetadata = true) {
        // If content is a collection, add each one separately
        if (!is_array($content) && !($content instanceof \Traversable) && !($content instanceof \IteratorAggregate)) {
            $content = array($content);
        }
        foreach ($content as $item) {
            $this->addContent($item);
        }
		
		// If the flag has been set, recalculate metadata
		if ($calculateMetadata === true) {
			$this->calculateMetadata();
		}
	}
    
    /**
     * Add a single new content item to the service response
     *
     * @param mixed $content  Content item to be added to the response
     * @return void
     */
    public function addContent($content) {
        $this->_content[] = $content;
    }
	
	/**
	 * Returns the currently set content block(s) for the service response
	 *
	 * @return array
	 */
	public function getContent() {
		return $this->_content;
	}
	
	/**
	 * Returns the first content block in the service response
	 *
	 * @return mixed
	 */
	public function getFirstContentItem() {
        if (isset($this->_content[0])) {
		  return $this->_content[0];
        }
        
        return null;
	}
	
	/**
	 * Clears any existing messages in the service response, and then sets the supplied
	 * array of new messages
	 *
	 * @param array $messages
	 * @return boolean
	 */
	public function setMessages(array $messages) {
		$this->clearMessages();
		return $this->addMessages($messages);
	}
	
	/**
	 * Adds the supplied array of messages to the existing set in the response
	 *
	 * @param array $messages
	 * @return boolean
	 * @throws \Tranquility\Services\ServiceException
	 */
	public function addMessages(array $messages) {
		foreach ($messages as $message) {
			// Check mandatory message fields have been provided
			if (!isset($message['code']) || !isset($message['text']) || !isset($message['level'])) {
				throw new ServiceException('Message must contain at least a code, text and level');
			}
			
			// Add message
			$fieldId = Utility::extractValue($message, 'fieldId', null);
            $textParameters = Utility::extractValue($message, 'textParameters', array());
			$this->addMessage($message['code'], $message['level'], $message['text'], $textParameters, $fieldId);
		}
		
		return true;
	}
	
	/**
	 * Add a single new error or information message to the existing set of messages 
	 * in the service response
	 *
	 * @param int    $code            The error code associated with the message
	 * @param string $level           Message level (defined in \Tranquility\Enum\MessageLevel)
     * @param string $text            The internal text key for the message text
	 * @param array  $textParameters  [Optional] Key/value array containing values to substitute into message text
	 * @param string $fieldId         [Optional] Relates a message to a particular form element or field
	 * @return boolean
	 * @throws \Tranquility\Services\ServiceException
	 */
	public function addMessage($code, $level, $text, $textParameters = array(), $fieldId = null) {
		// Validate message level
		if (!EnumMessageLevel::isValidValue($level)) {
			throw new ServiceException('Invalid message level supplied while adding to service response: '.$level);
		}
		
		// Add message to internal array
		$this->_messages[] = array(
			'code'    => $code,
			'text'    => $text,
            'params'  => $textParameters,
			'level'   => $level,
			'fieldId' => $fieldId	
		);
		return true;
	}
	
	/**
	 * Retrieve the set of messages contained in the service response
	 *
	 * @return array
	 */
	public function getMessages() {
		return $this->_messages;
	}
	
	/**
	 * Clear any existing messages contained in the service response
	 *
	 * @return boolean
	 */
	public function clearMessages() {
		$this->_messages = array();
		return true;
	}
	
	/**
	 * Set metadata properties directly for the service response. The properties
	 * set via this method will override any existing values that may have been
	 * automatically calculated.
	 *
	 * @param array $metadata
	 * @return boolean
	 * @throws \Tranquility\Services\ServiceException
	 */
	public function setMetadata(array $metadata = array()) {
		$merged = array_merge($this->_meta, $metadata);
		$this->_meta = $merged;
		return true;
	}
	
	/**
	 * Returns the metadata properties of the service response
	 *
	 * @return array
	 */
	public function getMetadata() {
		return $this->_meta;
	}
	
	/**
	 * Sets a unique transaction ID for the service response
	 *
	 * @param int $transactionId
	 * @return void
	 */
	public function setTransactionId($transactionId) {
		$this->_transactionId = $transactionId;
		$this->_meta['transactionId'] = $transactionId;
	}
	
	/**
	 * Returns the unique transaction ID associated with the service response
	 *
	 * @return int
	 */
	public function getTransactionId() {
		return $this->_transactionId;
	}
	
	/**
	 * Sets the HTTP response code for the service response (for use in REST APIs)
	 *
	 * @param int $code A valid HTTP status code (see \Tranquility\Enums\System\HttpStatusCode)
	 * @return boolean
	 * @throws \Tranquility\Services\ServiceException
	 */
	public function setHttpResponseCode($code) {
		// Validate HTTP response code
		if (!EnumHttpStatusCode::isValidValue($code)) {
			throw new ServiceException('Unknown HTTP response code: '.$code);
		}
		
		$this->_httpResponseCode = $code;
		return true;
	}
	
	/**
	 * Returns the HTTP response status code associated with the service response
	 * (for use in REST APIs)
	 *
	 * @return int
	 */
	public function getHttpResponseCode() {
		return $this->_httpResponseCode;
	}
	 
	
	/**
	 * Determine metadata properties for the service response, based on the content
	 * blocks currently in the service response
	 *
	 * @return array The metadata properties of the service response
	 */
	public function calculateMetadata() {
		// Count the number of items in the response
		$this->_meta['count'] = $this->getItemCount();
		
		// Determine HTTP response code
		$this->_meta['code'] = $this->getHttpResponseCode();
		
		// Transaction ID
		if ($this->getTransactionId() !== 0) {
			$this->_meta['transactionId'] = $this->getTransactionId();
		}
		
		// TODO: Additional metadata?
		return $this->_meta;
	}
	
	/**
	 * Return the number of items in the response content array
	 *
	 * @return int
	 */
	public function getItemCount() {
		$count = 0;
		if (is_array($this->_content) && reset($this->_content) !== false) {
			$count = count(reset($this->_content));
		} 
		return $count;
	}
	
	/**
	 * Return the number of messages in the response message array
	 *
	 * @return int
	 */
	public function getMessageCount() {
		return count($this->_messages);
	}
	
	/**
	 * Check to see whether a specified message code is present in the 
	 * messages already associated with the service response.
	 *
	 * @param int $code The message code to search for
	 * @return boolean
	 */
	public function containsMessageCode($code) {
		foreach ($this->_messages as $message) {
			if ($message['code'] == $code) {
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * Check to see if the service response contains any error messages, or
	 * if the HTTP response code is anything other than OK.
	 *
	 * @param string $threshold The level at which a message is considered an error. Defaults to 'error'.
	 * @return boolean
	 * @throws \Tranquility\Services\ServiceException
	 */
	public function containsErrors($threshold = EnumMessageLevel::Error) {
		// Check HTTP response code first
		if ($this->getHttpResponseCode() != EnumHttpStatusCode::OK) {
			return true;
		}
		
		// Validate error threshold
		if (!EnumMessageLevel::isValidValue($threshold)) {
			throw new ServiceException('Invalid message level provided: '.$threshold);
		}
		
		// Check message levels
		foreach ($this->_messages as $message) {
			if ($message['level'] == $threshold) {
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * Returns the full service response object, formatted as an array
	 *
	 * @return array
	 */
	public function toArray() {
		$response = array(
			'meta'     => $this->_meta,
			'messages' => $this->_messages,
			'content'  => $this->_content	
		);
		return $response;
	} 
}	