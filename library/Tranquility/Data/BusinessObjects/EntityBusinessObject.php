<?php namespace Tranquility\Data\BusinessObjects;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\Common\Collections\ArrayCollection;

use Tranquility\Enums\System\EntityType                                     as EnumEntityType;
use Tranquility\Enums\BusinessObjects\Address\AddressType                   as EnumAddressType;
use Tranquility\Data\BusinessObjects\AddressBusinessObject                  as Address;
use Tranquility\Data\BusinessObjects\AddressPhysicalBusinessObject          as AddressPhysical;
use Tranquility\Data\BusinessObjects\Extensions\AuditTrail                  as AuditTrail;
use Tranquility\Data\BusinessObjects\Extensions\Tags                        as Tag;
use Tranquility\Exceptions\BusinessObjectException                          as BusinessObjectException;

abstract class EntityBusinessObject {
    protected $id;
    protected $version;
    protected $type;
    protected $subType;
    protected $deleted;
    protected $auditTrail;
    protected $locks;
    
    // Related entities
    protected $addresses;
    protected $physicalAddresses;
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
     * @param mixed $data  May be an array or an instance of BusinessObject
     * @throws Tranquility\Exceptions\BusinessObjectException
     * @return Tranquility\Data\BusinessObjects\Entity
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
        $entityFields = $this->getEntityFields();
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
     * @return mixed
     */
    public function getTags() {
        return $this->tags->toArray();
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
    public function getAuditTrailDetails() {
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
        
        // Define inheritence
        $builder->setJoinedTableInheritance();
        $builder->setDiscriminatorColumn('type');
        $builder->addDiscriminatorMapClass(EnumEntityType::Person, PersonBusinessObject::class);
        $builder->addDiscriminatorMapClass(EnumEntityType::User, UserBusinessObject::class);
        $builder->addDiscriminatorMapClass(EnumEntityType::Address, AddressBusinessObject::class);
        $builder->addDiscriminatorMapClass(EnumEntityType::AddressPhysical, AddressPhysicalBusinessObject::class);
        
        // Define fields
        $builder->createField('id', 'integer')->isPrimaryKey()->generatedValue()->build();
        $builder->addField('version', 'integer');
        $builder->addField('deleted', 'boolean');
        
        // Add relationships
        $builder->createOneToOne('auditTrail', AuditTrail::class)->addJoinColumn('transactionId','transactionId')->build();
        $builder->createOneToMany('addresses', Address::class)->mappedBy('parentEntity')->build();
        $builder->createOneToMany('physicalAddresses', AddressPhysical::class)->mappedBy('parentEntity')->build();
        $builder->createManyToMany('tags', Tag::class)->inversedBy('entities')->setJoinTable('entity_tags_xref')->addJoinColumn('entityId', 'id')->addInverseJoinColumn('tagId', 'id')->build();
    }
    
    /**
     * Returns a list of all available fields for the business object
     *
     * @static
     * @return array
     */
    public static function getEntityFields() {
        return array_merge(self::$_fields, AuditTrail::getFields());
    }
    
    /**
     * Returns a list of fields required to create or update a new business object
     *
     * @static
     * @var boolean $newRecord  Adjusts the set of mandatory fields based on whether a record is being created or updated
     * @return array
     */
    public static function getMandatoryEntityFields($newRecord = false) {
        if (!$newRecord) {
            // ID will be mandatory for any updates to records
            $mandatoryFields = self::$_mandatoryFields;
            array_unshift($mandatoryFields, 'id');
            return $mandatoryFields;
        }
        return array_merge(self::$_mandatoryFields, AuditTrail::getMandatoryFields());
    }
}