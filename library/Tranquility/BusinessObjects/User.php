<?php namespace Tranquility\BusinessObjects;

use Illuminate\Contracts\Auth\Authenticatable as UserContract;

class User extends Entity implements UserContract {

	/**
     * Array of properties that are specific to a business object of a particular entity type
     * 
     * @var array
     * @static
     */
    protected static $_fields = array(
        'username',
        'password',
        'timezoneCode',
        'localeCode',
        'active',
        'securityGroupId',
        'registeredDateTime',
        'rememberToken'    
    );
    
    protected static $_mandatoryFields = array(
        'username',
		'timezoneCode',
		'localeCode',
		'active',
		'securityGroupId'
    );
    
    protected static $_mandatoryFieldsNewEntity = array(
        'password', 
		'passwordConfirm', 
		'parentId'
    );
    
	/**
	 * Create a new User business object
     *
     * @var array $data     [Optional] Initial values for object properties
     * @var array $options  [Optional] Configuration options for the object
     * @return void
     */
    public function __construct($data = array(), $options = array()) {
        parent::__construct($data, $options);
    } 

	/**
	 * Get the unique identifier for the user.
	 *
	 * @return mixed
	 */
	public function getAuthIdentifier()
	{
        $name = $this->getAuthIdentifierName();
		return $this->_values[$name];
	}
    
    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName()
    {
        return 'id';
    }

	/**
	 * Get the password for the user.
	 *
	 * @return string
	 */
	public function getAuthPassword()
	{
		return $this->attributes['password'];
	}

	/**
	 * Get the "remember me" token value.
	 *
	 * @return string
	 */
	public function getRememberToken()
	{
		return $this->attributes[$this->getRememberTokenName()];
	}

	/**
	 * Set the "remember me" token value.
	 *
	 * @param  string  $value
	 * @return void
	 */
	public function setRememberToken($value)
	{
		$this->attributes[$this->getRememberTokenName()] = $value;
	}

	/**
	 * Get the column name for the "remember me" token.
	 *
	 * @return string
	 */
	public function getRememberTokenName()
	{
		return 'rememberToken';
	}
    
    /**
     * Returns a list of all available fields for the User business object
     *
     * @static
     * @return array
     */
    public static function getEntityFields() {
        return array_merge(Entity::getEntityFields(), self::$_fields);
    }
    
    /**
     * Returns a list of fields required to create or update a new User business object
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
