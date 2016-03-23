<?php namespace Tranquility\Data\Objects\ExtensionObjects;

// Doctrine 2 libraries
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\Common\Collections\ArrayCollection;

// Tranquility libraries
use Tranquility\Data\Objects\DataObject                                     as DataObject;
use Tranquility\Exceptions\BusinessObjectException                          as BusinessObjectException;

abstract class ExtensionObject extends DataObject {
    /**
     * List of properties that can be accessed via getters and setters
     * 
     * @static
     * @var array
     */
    protected static $_fields = array();
    
    /**
     * Array of common properties that all Extension Objects will require
     * when creating or updating
     *
     * @static
     * @var array
     */
    protected static $_mandatoryFields = array();
    
    /**
     * Array of properties that are additionally mandatory only when creating a new Extension Object
     * 
     * @var array
     * @static
     */
    protected static $_mandatoryFieldsNewObject = array();
    
    /**
     * List of properties that are not publically accessible
     *
     * @static
     * @var array
     */
     protected static $_hiddenFields = array();
    
    /**
     * Create a new instance of the Extension Object
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
     * Sets values for object properties, based on the inputs provided
     * 
     * @abstract
     * @param mixed $data  May be an array or an instance of DataObject
     * @return Tranquility\Data\DataObject
     */
    public function populate($data) {
        if ($data instanceof ExtensionObject) {
            $data = $data->toArray();
        } elseif (is_object($data)) {
            $data = (array) $data;
        }
        if (!is_array($data)) {
            throw new BusinessObjectException('Initial data must be an array or object');
        }
        
        // Assign relevant data to object properties
        $entityFields = $this->getFields();
        foreach ($entityFields as $field) {
            if (isset($data[$field])) {
                $this->$field = $data[$field];
            }
        }
        
        return $this;
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
     * Returns a list of all available fields for the business object
     *
     * @static
     * @return array
     */
    public static function getFields() {
        return self::$_fields;
    }
    
    /**
     * Returns a list of fields required to create or update a new business object
     *
     * @static
     * @var boolean $newRecord  Adjusts the set of mandatory fields based on whether a record is being created or updated
     * @return array
     */
    public static function getMandatoryFields($newRecord = false) {
        if ($newRecord) {
            return array_merge(self::$_mandatoryFields, self::$_mandatoryFieldsNewObject);
        } else {
            return self::$_mandatoryFields;
        }
    }
    
    /**
     * Returns a list of fields that will not be exposed publically
     *
     * @static
     * @return array
     */
    protected static function _getHiddenFields() {
        return self::$_hiddenFields;
    }
}