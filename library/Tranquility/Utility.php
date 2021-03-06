<?php namespace Tranquility;

/**
 * Utility class containing useful shortcut methods
 *
 * @package \Tranquility
 * @author  Andrew Patterson <patto@live.com.au>
 */
 
class Utility {
	/**
	 * Constructor
	 * Cannot be instantiated - should be static only
	 */
	final public function __construct() {
		throw new Exception('\Tranquility\Utility class may not be instantiated');
	}
	
	/**
	 * Extracts the value for a specified key from an array or object
	 *
	 * @param mixed   $object    The array or object the value is stored in
	 * @param string  $key       The identifier for the value
	 * @param mixed   $default   [Optional] The value to return if no value is found in $object (defaults to null)
	 * @param string  $dataType  [Optional] The datatype to cast the returned value to
	 * @return mixed             The extracted value
	 */
	public static function extractValue($object, $key, $default = null, $dataType = null) {
		$value = null;
		
		// Determine if the object is an array or an actual object
		if (is_array($object) && isset($object[$key])) {
			$value = $object[$key];
		} elseif (is_object($object) && isset($object->$key)) {
			$value = $object->$key;
		}
		
		// If no value was extracted, return the default value
		if (is_null($value)) {
			return $default;
		}
		
		// Perform type casting on return value
		$dataType = strtolower($dataType);
		switch($dataType) {
			case 'string':
			case 'str':
				// Cast to string
				$value = strval($value);
				break;
			case 'integer':
			case 'int':
				// Cast to integer
				$value = intval($value);
				break;
			case 'float':
			case 'double':
				// Cast to decimal
				$value = floatval($value);
				break;
			case 'boolean':
			case 'bool':
				// Cast to boolean
				$value = (bool)$value;
				break;
			default:
				// No type cast necessary
				break;
		}
		
		// Return extracted value
		return $value;
	}
}