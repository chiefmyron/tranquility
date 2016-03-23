<?php namespace Tranquility\Data\Objects;

// Doctrine 2 libraries
use Doctrine\ORM\Mapping\ClassMetadata;

// Tranquility libraries
use Tranquility\Exceptions\DataObjectException as BusinessObjectException;

abstract class DataObject {
    /**
     * List of properties that can be accessed via getters and setters
     * 
     * @static
     * @var array
     */
    protected static $_fields = array();
    
    /**
     * List of properties required when creating or updating
     *
     * @static
     * @var array
     */
    protected static $_mandatoryFields = array();
    
    /**
     * List of properties that are not publically accessible
     *
     * @static
     * @var array
     */
     protected static $_hiddenFields = array();
    
    /**
     * Create a new instance of the Data Object
     *
     * @var array $data     [Optional] Initial values for object properties
     * @var array $options  [Optional] Configuration options for the object
     * @return void
     */
    abstract function __construct($data = array(), $options = array());
    
    /**
     * Sets values for object properties, based on the inputs provided
     * 
     * @abstract
     * @param mixed $data  May be an array or an instance of DataObject
     * @return Tranquility\Data\DataObject
     */
    abstract function populate($data); 
     
    /**
     * Returns a list of all available fields for the data object
     *
     * @static
     * @abstract
     * @return array
     */
    abstract static function getFields();
    
    /**
     * Returns a list of fields required to create or update a new data object
     *
     * @static
     * @abstract
     * @var boolean $newRecord  Adjusts the set of mandatory fields based on whether a record is being created or updated
     * @return array
     */
    abstract static function getMandatoryFields($newRecord = false);
    
    /**
     * Returns a list of fields that will not be exposed publically
     *
     * @static
     * @abstract
     * @return array
     */
    abstract protected static function _getHiddenFields();
}