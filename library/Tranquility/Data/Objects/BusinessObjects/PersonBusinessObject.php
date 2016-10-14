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
        'position'       => array('searchable'),
        'primaryContact' => array() // Only set when loaded via Contact relationship with an Account
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
        $builder->setCustomRepositoryClass('Tranquility\Data\Repositories\EntityRepository');
        
        // Define fields
        $builder->createField('title', 'string')->nullable()->build();
        $builder->addField('firstName', 'string');
        $builder->addField('lastName', 'string');
        $builder->addField('position', 'string');
        
        // Add relationships
        $builder->createOneToOne('user', User::class)->addJoinColumn('userId','id')->build();
        $builder->createOneToMany('contacts', Contact::class)->mappedBy('person')->orphanRemoval(true)->fetchLazy()->build();
    }

    //*************************************************************************
    // Class-specific getter methods                                          *
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
    public function setUserAccount($user) {
        $this->user = $user;
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

    public function _getAccount() {
        return $this->getAccount();
    }
}