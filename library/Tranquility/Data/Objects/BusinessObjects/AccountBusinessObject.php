<?php namespace Tranquility\Data\Objects\BusinessObjects;

// Doctrine 2 libraries
use Doctrine\ORM\Mapping                                                             as ORM;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\ArrayCollection;

// Tranquility libraries
use Tranquility\Enums\System\EntityType                                              as EnumEntityType;
use Tranquility\Exceptions\BusinessObjectException                                   as BusinessObjectException;

// Tranquility historical version of business object
use Tranquility\Data\Objects\BusinessObjects\History\AccountHistoricalBusinessObject as AccountHistory;

// Tranquility related business objects
use Tranquility\Data\Objects\ExtensionObjects\Contact                                as Contact;

class AccountBusinessObject extends BusinessObject {
    use \Tranquility\Data\Objects\BusinessObjects\Traits\PropertyAccessorTrait;
    
    // Object properties
    protected $name;
    
    // Related entities
    protected $contacts;
    
    /** 
     * Type of entity represented by the business object
     *
     * @var string
     * @static
     */
    protected static $_entityType = EnumEntityType::Account;

    /**
     * Name of the class responsible for representing historical versions of a Account entity
     * 
     * @var string
     * @static
     */
    protected static $_historicalEntityClass = AccountHistory::class;

    /**
     * Property definition for object
     * 
     * @static
     * @var array
     */
    protected static $_fieldDefinitions = array(
        'name' => array('mandatoryUpdate', 'mandatoryCreate', 'searchable')
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
        $builder->setTable('entity_accounts');
        $builder->setCustomRepositoryClass('Tranquility\Data\Repositories\EntityRepository');
        
        // Define fields
        $builder->addField('name', 'string');
        
        // Add relationships
        $builder->createOneToMany('contacts', Contact::class)->mappedBy('account')->orphanRemoval(true)->cascadePersist()->cascadeRemove()->build();
    }

    /**
     * Create a new instance of the Account
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


    //*************************************************************************
    // Contact relationship                                                   *
    //*************************************************************************

    /**
     * Retreive a collection of contacts associated with this Account
     *
     * @return mixed
     */
    public function getContacts() {
        return $this->contacts;
    }

    /**
     * Retrieve the primary contact for the Account
     *
     * @return mixed
     */
    public function getPrimaryContact() {
        $criteria = Criteria::create()->where(Criteria::expr()->eq("primaryContact", true));
        $contact = $this->contacts->matching($criteria);
        if ($contact->count() > 0) {
            return $contact[0];
        }

        return null;
    }

    /**
     * Add a Contact relationship to the Account
     *
     * @return \Tranquility\Data\BusinessObjects\AccountBusinessObject
     */
    public function addContact(Contact $contact) {
        if (!$this->contacts->contains($contact)) {
            $this->contacts->add($contact);
            $contact->setAccount($this);
        }

        return $this;
    }

    /**
     * Remove a Contact relationship from the Account
     *
     * @return \Tranquility\Data\BusinessObjects\AccountBusinessObject
     */
    public function removeContact(Contact $contact) {
        if ($this->contacts->contains($contact)) {
            $this->contacts->removeElement($contact);
            $contact->setAccount(null);
        }

        return $this;
    }
}