<?php namespace Tranquility\Html\FormError;

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
		
		// Check for field level messages
		foreach ($messages as $message) {
			if (isset($message['fieldId']) && $message['fieldId'] == $name) {
				$response = $response.'<div class="alert alert-inline alert-danger '.$this->_getClass($message['level']).'">'.trans('messages.'.$message['text']).'</div>'."\n";
			}
		}
		
		return $response;
	}
	
	private function _getClass($level) {
		return 'text-'.Utility::extractValue($this->_classes, $level, 'error');
	}
}
