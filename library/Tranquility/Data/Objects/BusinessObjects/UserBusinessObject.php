<?php namespace Tranquility\Data\Objects\BusinessObjects;

// Doctrine 2 libraries
use Doctrine\ORM\Mapping                                                            as ORM;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\ArrayCollection;

// Tranquility libraries
use Tranquility\Enums\System\EntityType                                             as EnumEntityType;
use Tranquility\Enums\System\UserTokenType                                          as EnumUserTokenType;
use Tranquility\Exceptions\BusinessObjectException                                  as BusinessObjectException;

// Tranquility historical version of business object
use Tranquility\Data\Objects\BusinessObjects\History\UserHistoricalBusinessObject   as UserHistory;

// Tranquility related business objects
use Tranquility\Data\Objects\BusinessObjects\PersonBusinessObject                   as Person;

// Tranquility extension data objects
use Tranquility\Data\Objects\ExtensionObjects\UserToken                             as UserToken;

// Laravel authenticatable contract
use Illuminate\Contracts\Auth\Authenticatable                                       as UserContract;

class UserBusinessObject extends BusinessObject implements UserContract {
    use \Tranquility\Data\Objects\BusinessObjects\Traits\PropertyAccessorTrait;
    
    protected $username;
    protected $password;
    protected $timezoneCode;
    protected $localeCode;
    protected $active;
    protected $securityGroupId;
    protected $registeredDateTime;
    
    // Related entities
    protected $person;
    
    // Related extension data objects
    protected $userTokens;

    /** 
     * Type of entity represented by the business object
     *
     * @var string
     * @static
     */
    protected static $_entityType = EnumEntityType::User;

    /**
     * Name of the class responsible for representing historical versions of this business entity
     * 
     * @var string
     * @static
     */
    protected static $_historicalEntityClass = UserHistory::class;

    /**
     * Property definition for object
     * 
     * @static
     * @var array
     */
    protected static $_fieldDefinitions = array(
        'username'           => array('mandatoryUpdate', 'mandatoryCreate', 'searchable'),
        'password'           => array('mandatoryUpdate', 'mandatoryCreate'),
        'passwordConfirm'    => array('mandatoryCreate'),
        'parentId'           => array('mandatoryCreate'),
        'timezoneCode'       => array('mandatoryUpdate', 'mandatoryCreate'),
        'localeCode'         => array('mandatoryUpdate', 'mandatoryCreate'),
        'active'             => array('mandatoryUpdate', 'mandatoryCreate'),
        'securityGroupId'    => array('mandatoryUpdate', 'mandatoryCreate'),
        'registeredDateTime' => array(),
    );
    
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
        $builder->createOneToOne('person', Person::class)->mappedBy('user')->build();
        $builder->createOneToMany('userTokens', UserToken::class)->mappedBy('user')->build();
    }
    
    //*************************************************************************
    // Class-specific getter methods                                          *
    //*************************************************************************
    
    /**
     * Create a new instance of the User entity
     *
     * @var array $data     [Optional] Initial values for object properties
     * @var array $options  [Optional] Configuration options for the object
     * @return void
     */
    public function __construct($data = array(), $options = array()) {
        parent::__construct($data, $options);
        
        // Initialise collections for related entities
        $this->userTokens = new ArrayCollection();
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
        $token = $this->getUserToken($this->getRememberTokenName());
        if (is_null($token)) {
            return "";
        } 
        
        return $token->tokenText;
	}
    
	/**
	 * Set the "remember me" token value.
	 *
	 * @param  string  $value
	 * @return void
	 */
	public function setRememberToken($value) {
        $this->setUserToken($this->getRememberTokenName(), $value);
	}
    
	/**
	 * Get the column name for the "remember me" token.
	 *
	 * @return string
	 */
	public function getRememberTokenName() {
		return EnumUserTokenType::RememberMe;
	}
    
    /**
     * Get the full name of the Person associated with the User
     *
     * @return string
     */
    public function getDisplayName() {
        return $this->person->getFullName();
    }
    
    /**
     * Get the parent Person associated with the User
     *
     * @return Person
     */
    public function getPerson() {
        return $this->person;
    }
    
    public function getUserToken($type) {
        // Check type is a valid token type
        if (!EnumUserTokenType::isValidValue($type)) {
            throw new Exception('Unknown user token type supplied: '.$type);
        }
        
        // Find existing token
        $criteria = Criteria::create()->where(Criteria::expr()->eq("type", $type));
        $token = $this->userTokens->matching($criteria)->first();
        if ($token === false) {
            return null;
        }
        return $token;
    }
    
    public function setUserToken($type, $value) {
        // Check if token already exists
        $token = $this->getUserToken($type);
        if (is_null($token)) {
            $token = new UserToken(array('user' => $this));
            $this->userTokens->add($token);
        }
        
        $token->setToken($type, $value);
    }
}