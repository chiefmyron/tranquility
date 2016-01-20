<?php namespace Tranquility\View;

use \Tranquility\View\ViewException           as ViewException;
use \Tranquility\Enums\System\MessageLevel    as EnumMessageLevel;
use \Tranquility\Enums\System\HttpStatusCode  as EnumHttpStatusCode;

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
     * @param array    $callbacks           An array containing JavaScript callbacks to be included in the response
     * @param int      $httpResposneCode    Override the default HTTP response code of the response
     * @return void
     */
    public function __construct(array $content = null, array $messages = null, array $callbacks = null, $httpResponseCode = null) {
        if (isset($content) && is_array($content)) {
            $this->addMultipleContentBlocks($content);
        }
        if (isset($messages) && is_array($messages)) {
            $this->addMessages($messages);
        }
        if (isset($callbacks) && is_array($callbacks)) {
            $this->addMultipleCallbacks($callbacks);
        }
        if (isset($httpResponseCode) && is_int($httpResponseCode)) {
            $this->setHttpResponseCode($httpResponseCode);
        }
    }
    
    /**
     * Assign a piece of rendered content to an HTML element
     *
     * @param string $element       HTML element ID to inject the content into
     * @param string $content       Rendered content
     * @param string $callback      JavaScript callback function
     * @param array  $callbackArgs  Array of arguments for the callback function
     * @return void
     */
    public function addContent($element, $content, $callback = null, $callbackArgs = null) {
        // Make sure any callback arguments are provided in an array
        if (!is_null($callbackArgs) && !is_array($callbackArgs)) {
            throw new ViewException('Any callback arguments specified as part of an AjaxResponse must be supplied as an array');
        }
        
        $this->content[] = array(
            'element'      => $element,
            'content'      => $content,
            'callback'     => $callback,
            'callbackArgs' => $callbackArgs
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
    
    /**
     * Clear one or more content items from the response
     *
     * @param string $element  If specified, clears content only for that element. Otherwise, clears content
     *                         for the entire response
     * @return void
     */ 
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
     * Adds multiple content blocks to the response
     *
     * @param array $items  An array of arrays containing content. Each content item should have
     *                      a key for 'element', 'content', 'callback' and 'callbackArgs'
     * @return void
     */
    public function addMultipleResponseElements(array $items) {
        foreach ($items as $item) {
            $element      = Utility::extractValue($items, 'element', null);
            $content      = Utility::extractValue($items, 'content', null);
            $callback     = Utility::extractValue($items, 'callback', null);
            $callbackArgs = Utility::extractValue($items, 'callbackArgs', null);
            $this->addContent($element, $content, $callback, $callbackArgs);
        }    
    }
    
    /**
     * Adds multiple messages to the response
     *
     * @param array $messages  Each element of the array should be it's own array, with keys for 'code', 
     *                         'text', 'level' and 'fieldId'
     * @param string $target  [Optional] Target for message. Blank for main page, 'dialog' to display inside modal
     * @return void
     */
    public function addMessages(array $messages, $target = null) {
        foreach ($messages as $message) {
            $this->addMessage($message['code'], $message['text'], $message['level'], $message['fieldId'], $target);
        }
    }
    
    /**
     * Add a new informational / error message to the response
     * 
     * @param int $code       Numeric error code for message
     * @param string $text    Message text
     * @param string $level   Message level (@see \Tranquility\Enums\System\MessageLevel)
     * @param string $fieldId [Optional] HTML entity ID to associate message with
     * @param string $target  [Optional] Target for message. Blank for main page, 'dialog' to display inside modal
     * @return void 
     */
    public function addMessage($code, $text, $level, $fieldId, $target = null) {
        // Validate message level
        if (!EnumMessageLevel::isValidValue($level)) {
            throw new ViewException('Message level "'.$level.'" is not valid');
        }
        
        // Add message to array
        $message = array (
            'code' => $code,
            'text' => $text,
            'level' => $level,
            'fieldId' => $fieldId,
            'target' => $target
        );
        $this->messages[] = $message;
    }
    
    /**
     * Add a JavaScript callback function to be executed in the response
     *
     * @param string $callback JavaScript callback function
     * @return void
     */
    public function addCallback($callback, $arguments = null) {
        // Make sure any callback arguments are provided in an array
        if (!is_null($arguments) && !is_array($arguments)) {
            throw new ViewException('Any callback arguments specified as part of an AjaxResponse must be supplied as an array');
        }
        
        $this->content[] = array(
            'element' => null,
            'content' => null,
            'callback' => $callback,
            'callbackArgs' => $arguments
        );
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
            'responseCode' => $this->httpResponseCode
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