<?php namespace Tranquility\View;

use \Tranquility\View\ViewException    as ViewException;
use \Tranquility\Enums\MessageLevel    as EnumMessageLevel;
use \Tranquility\Enums\HttpStatusCode  as EnumHttpStatusCode;

/**
 * Representation of an AJAX response message. Can be used internally via
 * an application, or externally via a REST API.
 *
 * @package Tranquility\View
 * @author  Andrew Patterson <patto@live.com.au>
 */

class AjaxResponse {
    
    /**
     * Array of HTML content to be injected into an element. The
     * array key should be the ID of the element to inject the content
     * into
     * @var array
     */
    public $content = array();
    
    /**
     * Array of field level validation messages
     * @var array
     */
    public $messages = array();
    
    /**
     * HTTP response code to use for the response
     * @var int
     */
    public $httpResponseCode = null;
     
    /**
     * Constructor
     *
     * @param array    $content             An array containing content items to be included in the response
     * @param array    $messages            An array of field-level validation messages to be included in the response
     * @param boolean  $displayModalDialog  Flag to trigger display of a modal dialog
     * @return void
     */
    public function __construct(array $content = null, array $messages = null, $httpResponseCode = null) {
        if (isset($content) && is_array($content)) {
            $this->addMultipleContentBlocks($content);
        }
        if (isset($messages) && is_array($messages)) {
            $this->addMessages($messages);
        }
        if (isset($httpResponseCode) && is_int($httpResponseCode)) {
            $this->setHttpResponseCode($httpResponseCode);
        }
        //$this->displayModalDialog = $displayModalDialog;
    }
    
    /**
     * Adds multiple content blocks to the response
     *
     * @param array $items  An array of arrays containing content. Each content item should have
     *                      a key for 'element', 'content' and 'callback'
     * @return void
     */
    public function addMultipleContentBlocks(array $items) {
        foreach ($items as $item) {
            $calllback = Utility::extractValue($items, 'callback', null);
            $this->addContent($item['element'], $item['content'], $callback);
        }    
    }
    
    /**
     * Assign a piece of rendered content to an HTML element
     *
     * @param string $element  HTML element ID to inject the content into
     * @param string $content  Rendered content
     * @param string $callback JavaScript callback function
     * @return void
     */
    public function addContent($element, $content, $callback = null) {
        $this->content[] = array(
            'element' => $element,
            'content' => $content,
            'callback' => $callback
        );
    }
    
    /**
     * Checks to see if the response already has content for a specified HTML element
     *
     * @param string $element  HTML element ID that the content is assigned to
     * @return boolean
     */
    public function hasContent($element) {
        foreach ($this->content as $item) {
            if ($item['element'] == $element) {
                return true;
            }
        }
        
        return false;
    }
    
     
    public function clearContent($element = null) {
        // If not element specified, clear all content
        if ($element === null) {
            $this->content = array();
            return;
        }
        
        // Attempt to find element in content array
        for($i = 0; $i < count($this->content); $i++) {
            if ($this->content[$i]['element'] == $element) {
                unset($this->content[$i]);
                $this->content = array_values($this->content);  // Re-index array elements
                return;
            }
        }
    }
    
    /**
     * Adds multiple messages to the response
     *
     * @param array $messages  Each element of the array should be it's own array, with keys for 'code', 
     *                         'text', 'level' and 'fieldId'
     * @return void
     */
    public function addMessages(array $messages) {
        foreach ($messages as $message) {
            $this->addMessage($message['code'], $message['text'], $message['level'], $message['fieldId']);
        }
    }
    
    /**
     * Add a new informational / error message to the response
     * 
     * @param int $code
     * @param string $text
     * @param string $level
     * @param string $fieldId
     * @return void 
     */
    public function addMessage($code, $text, $level, $fieldId) {
        // Validate message level
        if (!EnumMessageLevel::isValidValue($level)) {
            throw new ViewException('Message level "'.$level.'" is not valid');
        }
        if (is_null($fieldId)) {
            throw new ViewException('A Field ID must be specified when adding a validation message');
        }
        
        // Add message to array
        $message = array (
            'code' => $code,
            'text' => $text,
            'level' => $level,
            'fieldId' => $fieldId
        );
        $this->messages[] = $message;
    }
    
    /**
     * Sets the value of the HTTP response code to be used
     * 
     * @param int $httpResponseCode
     * @return void
     */
    public function setHttpResponseCode($httpResponseCode) {
        if (!EnumHttpStatusCode::isValidValue($httpResponseCode)) {
            throw new ViewException('The supplied HTTP response status code ('.$httpResponseCode.') is not valid.');
        }
        $this->httpResponseCode = $httpResponseCode;
    }
    
    /**
     * Returns a representation of the response object as an array
     *
     * @return array
     */
    public function toArray() {
        $response = array(
            'messages' => $this->messages,
            'content' => $this->content,
            'responseCode' => 200
        );
        return $response;
    }
    
    /**
     * Returns a representation of the response object as a serialised
     * JSON structure
     *
     * @return string
     */
    public function toJson() {
        $response = $this->toArray();
        return json_encode($response);
    }
}