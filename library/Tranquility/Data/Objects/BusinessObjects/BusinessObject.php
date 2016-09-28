<?php namespace Tranquility\Data\Objects\BusinessObjects;

// Doctrine 2 libraries
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;

// Tranquility libraries
use Tranquility\Enums\System\EntityType                                     as EnumEntityType;
use Tranquility\Enums\BusinessObjects\Address\AddressTypes                  as EnumAddressType;
use Tranquility\Data\Objects\DataObject                                     as DataObject;
use Tranquility\Exceptions\BusinessObjectException                          as BusinessObjectException;

// Tranquility related business objects
use Tranquility\Data\Objects\BusinessObjects\PersonBusinessObject           as Person;
use Tranquility\Data\Objects\BusinessObjects\UserBusinessObject             as User;
use Tranquility\Data\Objects\BusinessObjects\AccountBusinessObject          as Account;
use Tranquility\Data\Objects\BusinessObjects\ContactBusinessObject          as Contact;
use Tranquility\Data\Objects\BusinessObjects\AddressBusinessObject          as Address;
use Tranquility\Data\Objects\BusinessObjects\AddressPhysicalBusinessObject  as AddressPhysical;

// Tranquility extension data objects
use Tranquility\Data\Objects\ExtensionObjects\AuditTrail                    as AuditTrail;
use Tranquility\Data\Objects\ExtensionObjects\Tag                           as Tag;

abstract class BusinessObject extends DataObject {
    // Entity properties
    protected $id;
    protected $version;
    protected $type;
    protected $subType;
    protected $deleted;
    protected $locks;
    
    // Related business objects
    protected $addresses;
    protected $physicalAddresses;
    protected $relatedEntities;
    
    // Related extension data objects
    protected $auditTrail;
    protected $tags;
    
    /**
     * List of properties that can be accessed via getters and setters
     * 
     * @static
     * @var array
     */
    protected static $_fields = array(
        'id',
        'type',
        'version',
        'deleted',
    );
    
    /**
     * Array of common properties that all Business Objects will require
     * when creating or updating
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
     * Create a new instance of the Business Object
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
        
        // Ensure version and deleted properties are initialised
        if (!isset($this->version)) {
            $this->version = 1;
        }
        if (!isset($this->deleted)) {
            $this->deleted = 0;
        }
        
        // Initialise collections for related entities
        $this->tags = new ArrayCollection();
        $this->addresses = new ArrayCollection();
        $this->physicalAddresses = new ArrayCollection();
    }
    
    /**
     * Sets values for object properties, based on the inputs provided
     * 
     * @abstract
     * @param mixed $data  May be an array or an instance of DataObject
     * @return Tranquility\Data\DataObject
     */
    public function populate($data) {
        if ($data instanceof Entity) {
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
     * Retreive a collection of tags associated with this entity
     *
     * @return array
     */
    public function getTags() {
        return $this->tags->toArray();
    }
    
    /**
     * Remove a tag from the tag collection for the entity
     *
     * @param Tag $tag   Tag to be removed
     * @return Tranquility\Data\BusinessObjects\Entity
     */
    public function removeTag(Tag $tag) {
        if ($this->tags->contains($tag) === false) {
            return $this;
        }
        
        $this->tags->removeElement($tag);
        $tag->removeRelatedEntity($this);
        return $this;
    }
    
    /**
     * Add a new tag to the tag collection for the entity
     *
     * @param Tag $tag   New tag to add to the entity
     * @return Tranquility\Data\BusinessObjects\Entity
     */
    public function addTag(Tag $tag) {
        if ($this->tags->contains($tag) === true) {
            return $this;
        }
        
        $this->tags->add($tag);
        $tag->addRelatedEntity($this);
        return $this;
    }
    
    /**
     * Clears any existing tags and sets the new collection
     *
     * @param array $tags   Array of Tag objects
     * @return Tranquility\Data\BusinessObjects\Entity
     */
    public function setTags(array $tags) {
        // Clear existing tags
        $existingTags = $this->getTags();
        foreach ($existingTags as $tag) {
            $this->removeTag($tag);
            $tag->removeRelatedEntity($this);
        }
        
        // Add new tags
        foreach ($tags as $tag) {
            $this->addTag($tag);
            $tag->addRelatedEntity($this);
        }
        
        return $this;
    }

    /**
     * Retreive a collection of addresses associated with this entity
     *
     * @var string $category  [Optional] Category of address collection to return
     * @var string $type      [Optional] Type of address underneath the category
     * @var bool   $primary   [Optional] Returns addresses with the primary contact flag set this way (null to return all addresses)
     * @return mixed
     */
    public function getAddresses($category = null, $type = null, $primary = null) {
        // Build criteria to ensure we only retrieve active address records
        $addresses = array();
        
        // Only show addresses that have not been logically deleted
        $criteria = Criteria::create()->where(Criteria::expr()->eq("deleted", 0));
        
        // Filter by address type (if specified)
        if (!is_null($type)) {
            $crieria = $criteria->andWhere(Criteria::expr()->eq("addressType", $type));
        }

        // If request is for physical addresses, no more criteria can apply - return now
        if ($category == EnumAddressType::Physical) {
            $addresses = $this->physicalAddresses->matching($criteria);
            return $addresses->toArray();
        }

        // Add additional filters for non-physical addresses
        if (!is_null($category)) {
            $criteria = $criteria->andWhere(Criteria::expr()->eq("category", $category));
        }
        if (!is_null($primary)) {
            $criteria = $criteria->andWhere(Criteria::expr()->eq("primaryContact", $primary));
        }

        // Order to show primary contact first, and return
        $criteria = $criteria->orderBy(array("primaryContact" => Criteria::DESC));
        $addresses = $this->addresses->matching($criteria);
        return $addresses->toArray();
    }

    /**
     * Wrapper - retrieves only primary addresses for non-physical addresses
     *
     * @return mixed
     */
    public function getPrimaryAddresses() {
        $result = $this->getAddresses(null, null, true);

        // Format into key => value array, using address category as the key
        foreach ($result as $address) {
            $addresses[$address->category] = $address;
        }

        return $addresses;
    }

    /** 
     * Wrapper - return only the primary address for the specified address category
     *
     * @return mixed
     */
    public function getPrimaryAddress($category) {
        $addresses = $this->getAddresses($category, null, true);
        
        // If no primary address is set, return null
        if (count($addresses) <= 0) {
            return null;
        }

        return $addresses[0];
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
     * Set the audit trail details for an entity
     *
     * @param $auditTrail \Tranquility\Data\BusinessObject\Extensions\AuditTrail
     * @return void
     */
    public function setAuditTrail($auditTrail) {
        if (!($auditTrail instanceof AuditTrail)) {
            throw new BusinessObjectException('Audit trail information must be provided as a \Tranquility\Data\BusinessObjects\Extensions\AuditTrail object');
        }
        
        $this->auditTrail = $auditTrail;
    }
    
    /**
     * Retrieve audit trail details for the entity as an array
     *
     * @return \Tranquility\Data\BusinessObjects\Extensions\AuditTrail
     */
    public function getAuditTrail() {
        return $this->auditTrail;
    }
     
    /**
     * Metadata used to define object relationship to database
     *
     * @var \Doctrine\ORM\Mapping\ClassMetadata $metadata  Metadata to be passed to Doctrine
     * @return void
     */
    public static function loadMetadata(ClassMetadata $metadata) {
        $builder = new ClassMetadataBuilder($metadata);
        // Define table name
        $builder->setTable('entity');
        $builder->setCustomRepositoryClass('Tranquility\Data\Repositories\EntityRepository');
        
        // Define inheritence
        $builder->setJoinedTableInheritance();
        $builder->setDiscriminatorColumn('type');
        $builder->addDiscriminatorMapClass(EnumEntityType::Person, Person::class);
        $builder->addDiscriminatorMapClass(EnumEntityType::User, User::class);
        $builder->addDiscriminatorMapClass(EnumEntityType::Account, Account::class);
        $builder->addDiscriminatorMapClass(EnumEntityType::Address, Address::class);
        $builder->addDiscriminatorMapClass(EnumEntityType::AddressPhysical, AddressPhysical::class);
        
        // Define fields
        $builder->createField('id', 'integer')->isPrimaryKey()->generatedValue()->build();
        $builder->addField('version', 'integer');
        $builder->addField('deleted', 'boolean');
        
        // Add relationships
        $builder->createOneToOne('auditTrail', AuditTrail::class)->addJoinColumn('transactionId','transactionId')->build();
        $builder->createOneToMany('addresses', Address::class)->mappedBy('parentEntity')->build();
        $builder->createOneToMany('physicalAddresses', AddressPhysical::class)->mappedBy('parentEntity')->build();
        $builder->createManyToMany('tags', Tag::class)->inversedBy('entities')->setJoinTable('entity_tags_xref')->addJoinColumn('entityId', 'id')->addInverseJoinColumn('tagId', 'id')->build();
        $builder->createManyToMany('relatedEntities', BusinessObject::class)->setJoinTable('entity_entity_xref')->addJoinColumn('parentId', 'id')->addInverseJoinColumn('childId', 'id')->build();
    }
    
    /**
     * Returns a list of all available fields for the business object
     *
     * @static
     * @return array
     */
    public static function getFields() {
        return array_merge(self::$_fields, AuditTrail::getFields());
    }
    
    /**
     * Returns a list of fields required to create or update a new business object
     *
     * @static
     * @var boolean $newRecord  Adjusts the set of mandatory fields based on whether a record is being created or updated
     * @return array
     */
    public static function getMandatoryFields($newRecord = false) {
        if (!$newRecord) {
            // ID will be mandatory for any updates to records
            $mandatoryFields = self::$_mandatoryFields;
            array_unshift($mandatoryFields, 'id');
            return $mandatoryFields;
        }
        return array_merge(self::$_mandatoryFields, AuditTrail::getMandatoryFields($newRecord));
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