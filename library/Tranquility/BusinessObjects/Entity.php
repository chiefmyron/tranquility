<?php namespace Tranquility\BusinessObjects;

use Tranquility\Enums\System\Entity as EnumEntityType;

abstract class Entity {
    /**
     * Array of common properties that all Business Objects will contain
     * 
     * @static
     * @var array
     */
    protected static $_fields = array(
        'id',
        'type',
        'subType',
        'version',
        'deleted',
        'locked',
        'lockedBy',
        'lockedDatetime',
        'updateBy',
        'updateDateTime',
        'updateReason',
        'transactionId',
        'transactionSource',
    );
    
    /**
     * Array of common properties that all Business Objects will require
     * when creating or updating
     *
     * @static
     * @var array
     */
    protected static $_mandatoryFields = array(
        'type',
		'transactionSource',
		'updateBy',
		'updateDatetime',
		'updateReason'
    );
    
    /**
     * Array of values for properties in the business object
     * 
     * @var array
     */  
    protected $_values = array();
    
    /**
     * Array of business object types that can be children of this business object
     *
     * @var array
     */
    protected $_childEntityTypes = array();
    
    /**
     * Create a new instance of the Business Object
     *
     * @var array $data     [Optional] Initial values for object properties
     * @var array $options  [Optional] Configuration options for the object
     * @return void
     */
    public function __construct($data = array(), $options = array()) {
        // Set values for valid properties
        if (count($data) > 0) {
            $this->populate($data);
        }
    }
    
    /**
     * Set the value for an object property
     * 
     * @param string $name  Property name
     * @param mixed $value  Property value
     * @throws Tranquility\BusinessObjects\BusinessObjectException 
     * @return void
     */
    public function __set($name, $value) {
        $methodName = '_set'.ucfirst($name);
        if (method_exists($this, $methodName)) {
            // Use custom function to set value
            $this->{$methodName}($value);
        } elseif (in_array($name, $this->getEntityFields())) {
            // Store value directly
            $this->_values[$name] = $value;
        } else {
            throw new BusinessObjectException('Cannot set property as "'.get_class($this).'" does not have a property named "'.$name.'"');
        }
    }
    
    /**
     * Retrieves the value for an object property
     * 
     * @param string $name  Property name
     * @throws Tranquility\BusinessObjects\BusinessObjectException
     * @return mixed
     */
    public function __get($name) {
        $methodName = '_get'.ucfirst($name);
        if (method_exists($this, $methodName)) {
            // Use custom function to retrieve value
            return $this->{$methodName}();
        } elseif (in_array($name, $this->getEntityFields())) {
            // Retrieve value directly
            if (!array_key_exists($name, $this->_values)) {
                return null;
            }
            return $this->_values[$name];
        } else {
            throw new BusinessObjectException('Cannot get property value as "'.get_class($this).'" does not have a property named "'.$name.'"');
        }
    }
    
    /**
     * Retrieves value for an object property for display in a form
     * Added for compatibility with laravelcollective/html package forms
     *
     * @var string $name  Property name
     * @return mixed
     */
    public function getFormValue($name) {
        return $this->__get($name);
    }
    
    /**
     * Checks whether a value has been set for an object property
     * 
     * @param string $name  Property name
     * @return boolean
     */
    public function __isset($name) {
        if (in_array($name, $this->_fields)) {
            return isset($this->_values[$name]);
        } else {
            return false;
        }
    }
    
    /**
     * Unsets the value of an object property
     * 
     * @param string $name  Property name
     * @return void
     */
    public function __unset($name) {
        if (isset($this->$name)) {
            $this->_values[$name] = null;
        }
    }
    
    /**
     * Sets values for object properties, based on the inputs provided
     * 
     * @param mixed $data  May be an array or an instance of BusinessObject
     * @throws Tranquility\BusinessObjects\BusinessObjectException
     * @return Tranquility\BusinessObjects\Entity
     */
    public function populate($data) {
        if ($data instanceof Entity) {
            $data = $data->toArray();
        } elseif (is_object($data)) {
            $data = (array) $data;
        }
        if (!is_array($data)) {
            throw new BusinessObjectException('Initial data must be an array or object');
        }
        
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
        
        return $this;
    }
    
    /**
     * Convert object into an array
     *
     * @return array
     */
    public function toArray() {
        return $this->_values;
    }
    
    /**
     * Returns a list of all available fields for the business object
     *
     * @static
     * @return array
     */
    public static function getEntityFields() {
        return self::$_fields;
    }
    
    /**
     * Returns a list of fields required to create or update a new business object
     *
     * @static
     * @var boolean $newRecord  Adjusts the set of mandatory fields based on whether a record is being created or updated
     * @return array
     */
    public static function getMandatoryEntityFields($newRecord = false) {
        if (!$newRecord) {
            // ID will be mandatory for any updates to records
            $mandatoryFields = self::$_mandatoryFields;
            array_unshift($mandatoryFields, 'id');
            return $mandatoryFields;
        }
        return self::$_mandatoryFields;
    }
}