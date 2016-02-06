<?php namespace Tranquility\Data\BusinessObjects;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\Common\Collections\ArrayCollection;

use Tranquility\Data\BusinessObjects\PersonBusinessObject as Person;
use Tranquility\Data\BusinessObjects\Extensions\UserTokens;
use Tranquility\Data\BusinessObjects\History\UserHistoricalBusinessObject;

use Illuminate\Contracts\Auth\Authenticatable as UserContract;

class UserBusinessObject extends EntityBusinessObject implements UserContract {
    use \Tranquility\Data\Traits\PropertyAccessorTrait;
    
    protected $username;
    protected $password;
    protected $timezoneCode;
    protected $localeCode;
    protected $active;
    protected $securityGroupId;
    protected $registeredDateTime;
    protected $userTokens;
    protected $person;
    
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
    );
    
    /**
     * Array of properties that are mandatory when creating or updating a business object
     * 
     * @var array
     * @static
     */
    protected static $_mandatoryFields = array(
        'username',
		'timezoneCode',
		'localeCode',
		'active',
		'securityGroupId'
    );
    
    /**
     * Array of properties that are additionally mandatory only when creating a business object
     * 
     * @var array
     * @static
     */
    protected static $_mandatoryFieldsNewEntity = array(
        'password', 
		'passwordConfirm', 
		'parentId'
    );
    
    /**
     * Array of properties that will not be displayed externally
     *
     * @static
     * @var array
     */
    protected static $_hiddenFields = array(
        'password'
    );
    
    /**
     * Name of the class responsible for representing historical versions of this business entity
     * 
     * @var string
     * @static
     */
    protected static $_historicalEntityClass = UserHistoricalBusinessObject::class;
    
    /**
     * Metadata used to define object relationship to database
     *
     * @var \Doctrine\ORM\Mapping\ClassMetadata $metadata  Metadata to be passed to Doctrine
     * @return void
     */
    public static function loadMetadata(ClassMetadata $metadata) {
        $builder = new ClassMetadataBuilder($metadata);
        // Define table name
        $builder->setTable('entity_users');
        $builder->setCustomRepositoryClass('Tranquility\Data\Repositories\UserRepository');
        
        // Define fields
        $builder->addField('username', 'string');
        $builder->addField('password', 'string');
        $builder->addField('timezoneCode', 'string');
        $builder->addField('localeCode', 'string');
        $builder->addField('active', 'boolean');
        $builder->addField('securityGroupId', 'integer');
        $builder->addField('registeredDateTime', 'datetime');
        
        // Add relationships
        $builder->createOneToOne('userTokens', UserTokens::class)->addJoinColumn('id','userId')->build();
        $builder->createOneToOne('person', Person::class)->mappedBy('user')->build();
    }
    
    /**
	 * Get the unique identifier for the user.
	 *
	 * @return mixed
	 */
	public function getAuthIdentifier() {
        $name = $this->getAuthIdentifierName();
		return $this->__get($name);
	}
    
    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName() {
        return 'id';
    }
    
	/**
	 * Get the password for the user.
	 *
	 * @return string
	 */
	public function getAuthPassword() {
		return $this->password;
	}
    
	/**
	 * Get the "remember me" token value.
	 *
	 * @return string
	 */
	public function getRememberToken() {
        return $this->userTokens->getRememberToken();
	}
    
	/**
	 * Set the "remember me" token value.
	 *
	 * @param  string  $value
	 * @return void
	 */
	public function setRememberToken($value) {
        $this->userTokens->setRememberToken($value);
	}
    
	/**
	 * Get the column name for the "remember me" token.
	 *
	 * @return string
	 */
	public function getRememberTokenName() {
		return 'rememberToken';
	}
    
    public function getDisplayName() {
        return $this->person->getFullName();
    }
}