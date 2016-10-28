<?php namespace Tranquility\Data\Objects\BusinessObjects;

// Doctrine 2 libraries
use Doctrine\ORM\Mapping                                                            as ORM;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\ArrayCollection;

// Tranquility libraries
use Tranquility\Enums\System\EntityType                                             as EnumEntityType;
use Tranquility\Enums\BusinessObjects\Address\AddressTypes                          as EnumAddressType;
use Tranquility\Exceptions\BusinessObjectException                                  as BusinessObjectException;

// Tranquility historical version of business object
use Tranquility\Data\Objects\BusinessObjects\History\PersonHistoricalBusinessObject as PersonHistory;

// Tranquility related business objects
use Tranquility\Data\Objects\BusinessObjects\UserBusinessObject                     as User;
use Tranquility\Data\Objects\ExtensionObjects\Contact                               as Contact;

class PersonBusinessObject extends BusinessObject {
    use \Tranquility\Data\Objects\BusinessObjects\Traits\PropertyAccessorTrait;
    
    // Object properties
    protected $title;
    protected $firstName;
    protected $lastName;
    protected $position;

    // Temporary property (not persisted)
    protected $primaryContact;
    
    // Related entities
    protected $user;
    protected $contacts;

    /** 
     * Type of entity represented by the business object
     *
     * @var string
     * @static
     */
    protected static $_entityType = EnumEntityType::Person;

    /**
     * Name of the class responsible for representing historical versions of a Person entity
     * 
     * @var string
     * @static
     */
    protected static $_historicalEntityClass = PersonHistory::class;

    /**
     * Property definition for object
     * 
     * @static
     * @var array
     */
    protected static $_fieldDefinitions = array(
        'title'          => array(),
        'firstName'      => array('mandatoryUpdate', 'mandatoryCreate', 'searchable'),
        'lastName'       => array('mandatoryUpdate', 'mandatoryCreate', 'searchable'),
        'position'       => array('searchable')
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
        $builder->setTable('entity_people');
        $builder->setCustomRepositoryClass('Tranquility\Data\Repositories\PersonRepository');
        
        // Define fields
        $builder->createField('title', 'string')->nullable()->build();
        $builder->addField('firstName', 'string');
        $builder->addField('lastName', 'string');
        $builder->addField('position', 'string');
        
        // Add relationships
        $builder->createOneToOne('user', User::class)->addJoinColumn('userId','id')->build();
        $builder->createOneToMany('contacts', Contact::class)->mappedBy('person')->orphanRemoval(true)->fetchLazy()->build();
    }
    
    /**
     * Create a new instance of the Person entity
     *
     * @var array $data     [Optional] Initial values for object properties
     * @var array $options  [Optional] Configuration options for the object
     * @return void
     */
    public function __construct($data = array(), $options = array()) {
        parent::__construct($data, $options);
        
        // Initialise collections for related entities
        $this->contacts = new ArrayCollection();
    }

    //*************************************************************************
    // Class-specific getter and setter methods                               *
    //*************************************************************************

    /**
     * Retrieve formatted name for person
     *
     * @return string
     */
    public function getFullName($includeTitle = false) {
        $name = $this->firstName.' '.$this->lastName;
        if ($includeTitle) {
            $name = $this->title.' '.$name;
        }
        return $name;
    }

    //*************************************************************************
    // User relationship                                                      *
    //*************************************************************************
    
    /**
     * Retrieve the User object associated with this person
     *
     * @return \Tranquility\Data\BusinessObjects\UserBusinessObject
     */
    public function getUserAccount() {
        return $this->user;
    }
    
    /**
     * Associates a user account with this person
     * 
     * @param User $user  User account to be associated with the person
     * @return Person
     */
    public function setUserAccount(User $user) {
        $this->user = $user;
        return $this;
    }

    //*************************************************************************
    // Contact relationship                                                   *
    //*************************************************************************

    /** 
     * Retrieve the Contact relationship object associated with this Person
     *
     * @return \Tranquility\Data\ExtensionObjects\Contact
     */
    public function getContact() {
        if (count($this->contacts) > 0) {
            return $this->contacts[0];
        }

        return null;
    }

    /**
     * Remove a Contact relationship from the Person
     *
     * @return \Tranquility\Data\BusinessObjects\PersonBusinessObject
     */
    public function removeContact(Contact $contact) {
        if ($this->contacts->contains($contact)) {
            $this->contacts->removeElement($contact);
            $contact->setPerson(null);
        }

        return $this;
    }

    /**
     * Retrieve the Account object associated with this person
     *
     * @return \Tranquility\Data\BusinessObjects\AccountBusinessObject
     */
    public function getAccount() {
        $contacts = $this->contacts;
        if (count($contacts) > 0) {
            return $contacts[0]->getAccount();
        }

        return null;
    }

    /**
     * Retrieves value for an object property for display in a form
     * Added for compatibility with laravelcollective/html package forms
     *
     * @var string $name  Property name
     * @return mixed
     */
    public function getFormValue($name) {
        // 'accountId' is used in the create / update form to associate an Account with the Person
        if ($name == 'accountId') {
            $account = $this->getAccount();
            if (!is_null($account)) {
                return $account->id.':'.$account->name;
            } else {
                return null;
            }
        }

        // For all other properties, retrieve from class variable
        return $this->__get($name);
    }
}