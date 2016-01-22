<?php namespace Tranquility\BusinessObjects;

class Person extends Entity {

	/**
     * Array of properties that are specific to a business object of a particular entity type
     * 
     * @var array
     * @static
     */
    protected static $_fields = array(
        'title',
        'firstName',
        'lastName',
        'position'
    );
    
    protected static $_mandatoryFields = array(
		'firstName',
        'lastName'
    );
    
    protected static $_mandatoryFieldsNewEntity = array();
    
	/**
	 * Create a new Person business object
     *
     * @var array $data     [Optional] Initial values for object properties
     * @var array $options  [Optional] Configuration options for the object
     * @return void
     */
    public function __construct($data = array(), $options = array()) {
        parent::__construct($data, $options);
    } 

    /**
     * Get the full name for the person
     *
     * @var boolean $includeTitle  If true, prepends the name with the person's title
     * @return string
     */
    public function getName($includeTitle = false) {
        $name = $this->firstName.' '.$this->lastName;
        if ($includeTitle) {
            $name = $this->title.' '.$name;
        }
        return $name;
    }
    
    /**
     * Returns a list of all available fields for the Person business object
     *
     * @static
     * @return array
     */
    public static function getEntityFields() {
        return array_merge(Entity::getEntityFields(), self::$_fields);
    }
    
    /**
     * Returns a list of fields required to create or update a new Person business object
     *
     * @static
     * @var boolean $newRecord  Adjusts the set of mandatory fields based on whether a record is being created or updated
     * @return array
     */
    public static function getMandatoryEntityFields($newRecord = false) {
        $fields = array_merge(Entity::getMandatoryEntityFields($newRecord), self::$_mandatoryFields);
        if ($newRecord) {
            $fields = array_merge($fields, self::$_mandatoryFieldsNewEntity);
        }
        return $fields;
    }
}