<?php namespace Tranquility\Data\Objects\BusinessObjects\Traits;

use Tranquility\Data\Objects\BusinessObjects\BusinessObject        as Entity;
use Tranquility\Data\Objects\ExtensionObjects\AuditTrail           as AuditTrail;
use Tranquility\Exceptions\BusinessObjectException                 as BusinessObjectException;

trait PropertyAccessorTrait {
    /**
     * Set the value for an object property
     * 
     * @param string $name  Property name
     * @param mixed $value  Property value
     * @throws Tranquility\Exceptions\BusinessObjectException 
     * @return void
     */
    public function __set($name, $value) {
        $methodName = '_set'.ucfirst($name);
        if (method_exists($this, $methodName)) {
            // Use custom function to set value
            $this->{$methodName}($value);
        } elseif (in_array($name, self::getFields())) {
            // Store value directly
            if ($value !== '') {
                $this->$name = $value;
            }
        } else {
            throw new BusinessObjectException('Cannot set property - class "'.get_class($this).'" does not have a property named "'.$name.'"');
        }
    }
    
    /**
     * Retrieves the value for an object property
     * 
     * @param string $name  Property name
     * @throws Tranquility\Exceptions\BusinessObjectException
     * @return mixed
     */
    public function __get($name) {
        $methodName = '_get'.ucfirst($name);
        if (method_exists($this, $methodName)) {
            // Use custom function to retrieve value
            return $this->{$methodName}();
        } elseif (in_array($name, self::getFields()) && !in_array($name, self::_getHiddenFields())) {
            // Retrieve value directly
            return $this->$name;
        } else {
            throw new BusinessObjectException('Cannot get property value - class "'.get_class($this).'" does not have a property named "'.$name.'"');
        }
    }
    
    /**
     * Checks whether a value has been set for an object property
     * 
     * @param string $name  Property name
     * @return boolean
     */
    public function __isset($name) {
        if (in_array($name, self::getFields())) {
            return isset($this->$name);
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
            $this->$name = null;
        }
    }
    
    /**
     * Convert object into an array
     *
     * @return array
     */
    public function toArray() {
        $result = array();
        foreach (self::getFields() as $fieldName) {
            if (!in_array($fieldName, AuditTrail::getFields()) && !in_array($fieldName, $this->_getHiddenFields())) {
                $result[$fieldName] = $this->$fieldName;
            }
        }
        
        // Add in audit trail details
        $auditTrail = $this->getAuditTrail();
        $result['auditTrail'] = $auditTrail;
        
        return $result;
    }
    
    /**
     * Returns a list of all available fields for the business object
     *
     * @static
     * @return array
     */
    public static function getFields() {
        return array_merge(Entity::getFields(), self::$_fields);
    }
    
    /**
     * Returns a list of fields required to create or update a new business object
     *
     * @static
     * @var boolean $newRecord  Adjusts the set of mandatory fields based on whether a record is being created or updated
     * @return array
     */
    public static function getMandatoryFields($newRecord = false) {
        $fields = array_merge(Entity::getMandatoryFields($newRecord), self::$_mandatoryFields);
        if ($newRecord) {
            $fields = array_merge($fields, self::$_mandatoryFieldsNewEntity);
        }
        return $fields;
    }
    
    /**
     * Returns a list of fields that will not be exposed publically
     *
     * @static
     * @abstract
     * @return array
     */
    protected static function _getHiddenFields() {
        if (!isset(self::$_hiddenFields)) {
            return array();
        }
        return self::$_hiddenFields;
    }
    
    /**
     * Returns the name of the class used to model the historical records for this business object
     *
     * @return string
     */
    public static function getHistoricalEntityClass() {
        return self::$_historicalEntityClass;
    }
    
    /**
     * Returns the entity type of the business object
     *
     * @static
     * @return string
     */
    public static function getEntityType() {
        return self::$_entityType;
    }
}