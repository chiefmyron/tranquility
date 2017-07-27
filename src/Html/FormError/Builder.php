<?php namespace Tranquility\Html\FormError;

use Illuminate\Support\HtmlString;
use Illuminate\Support\Traits\Macroable;

use Tranquility\Utility;
use Tranquility\Enums\System\MessageLevel as EnumMessageLevel;

class Builder {

	use Macroable;
	
	protected $_classes = array(
		EnumMessageLevel::Error => 'danger',
		EnumMessageLevel::Warning => 'warning',
		EnumMessageLevel::Info => 'info',
		EnumMessageLevel::Success => 'success'
	);
		
	/**
	 * Display an inline error message as a span
	 */
	public function inline($name, $messages = array(), $options = array()) {
		$response = '';
		
		// Check if there are any messages to work with
		if (!is_array($messages) || count($messages) == 0) {
			return;
		}
		
		$messageText = array(
            'error' => array(), 
            'warning' => array(), 
            'success' => array(), 
            'info' => array()
        );

		// Check for field level messages
		foreach ($messages as $message) {
			if (isset($message['fieldId']) && $message['fieldId'] == $name) {
				$messageText[$message['level']][] = $message['text'];
			}
		}

		foreach ($messageText as $level => $messageStrings) {
			if (count($messageStrings) > 0) {
				$html = view('administration._partials.errors-inline', ['fieldId' => $name, 'messages' => $messageStrings, 'level' => $level]);
				$response = $response.$html;
			}
		}
		
		return $response;
	}
}
