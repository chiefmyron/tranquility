<?php namespace Tranquility\Data\Objects\BusinessObjects\Traits;

use Tranquility\Data\Objects\BusinessObjects\BusinessObject        as Entity;
use Tranquility\Data\Objects\ExtensionObjects\AuditTrail           as AuditTrail;
use Tranquility\Exceptions\BusinessObjectException                 as BusinessObjectException;

trait PropertyAccessorTrait {
    /**
     * List of properties that can be accessed via getters and setters
     * 
     * @static
     * @var array
     */
    protected static $_fields;
    
    /**
     * Array of common properties that all Business Objects will require
     * when creating or updating
     *
     * @static
     * @var array
     */
    protected static $_mandatoryFields;

    /**
     * List of properties that are searchable
     *
     * @static
     * @var array
     */
    protected static $_searchableFields;
    
    /**
     * List of properties that are not publically accessible
     *
     * @static
     * @var array
     */
     protected static $_hiddenFields;
     
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
        // If getting for the first time, build a static list of entity fields
        if (static::$_fields === null) {
            // Generate list of fields from definitions
            $entityFields = array_keys(self::$_fieldDefinitions);
            static::$_fields = array_merge(Entity::getFields(), $entityFields);
        }
        return static::$_fields;
    }
    
    /**
     * Returns a list of fields required to create or update a new business object
     *
     * @static
     * @var boolean $newRecord  Adjusts the set of mandatory fields based on whether a record is being created or updated
     * @return array
     */
    public static function getMandatoryFields($newRecord = false) {
        // If getting for the first time, build a static list of mandatory fields
        if (static::$_mandatoryFields === null) {
            $mandatoryFields = array('update' => array(), 'create' => array());

            $entityFields = self::$_fieldDefinitions;
            foreach ($entityFields as $fieldName => $definition) {
                if (in_array('mandatoryUpdate', $definition)) {
                    $mandatoryFields['update'][] = $fieldName;
                }
                if (in_array('mandatoryCreate', $definition)) {
                    $mandatoryFields['create'][] = $fieldName;
                }
            }

            if ($newRecord) {
                $mandatoryFields['create'] = array_merge($mandatoryFields['create'], AuditTrail::getMandatoryFields($newRecord));
            } else {
                $mandatoryFields['update'] = array_merge($mandatoryFields['update'], AuditTrail::getMandatoryFields($newRecord));
            }
            static::$_mandatoryFields = $mandatoryFields;
        }

        // Get mandatory fields (different for create / update actions)
        if ($newRecord) {
            return static::$_mandatoryFields['create'];
        } else {
            return static::$_mandatoryFields['update'];
        }
    }
    
    /**
     * Returns a list of fields used for search
     *
     * @static
     * @return array
     */
    public static function getSearchableFields() {
        // If getting for the first time, build a static list of mandatory fields
        if (static::$_searchableFields === null) {
            $searchableFields = array();

            $entityFields = self::$_fieldDefinitions;
            foreach ($entityFields as $fieldName => $definition) {
                if (in_array('searchable', $definition)) {
                    $searchableFields[] = $fieldName;
                }
            }
            static::$_searchableFields = array_merge($searchableFields, Entity::getSearchableFields());
        }

        return static::$_searchableFields;
    }
    
    /**
     * Returns a list of fields that will not be exposed publically
     *
     * @static
     * @return array
     */
    public static function getHiddenFields() {
        // If getting for the first time, build a static list of mandatory fields
        if (static::$_hiddenFields === null) {
            $hiddenFields = array();

            $entityFields = self::$_fieldDefinitions;
            foreach ($entityFields as $fieldName => $definition) {
                if (in_array('hidden', $definition)) {
                    $hiddenFields[] = $fieldName;
                }
            }
            static::$_hiddenFields = array_merge($hiddenFields, Entity::getHiddenFields());
        }

        return static::$_hiddenFields;
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