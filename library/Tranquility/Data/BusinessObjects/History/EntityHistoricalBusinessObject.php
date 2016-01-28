<?php namespace Tranquility\Data\BusinessObjects\History;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\Common\Collections\ArrayCollection;

use Tranquility\Data\BusinessObjects\Extensions\AuditTrail as AuditTrail;
use Tranquility\Data\BusinessObjects\EntityBusinessObject  as Entity;
use Tranquility\Exceptions\BusinessObjectException         as BusinessObjectException;

abstract class EntityHistoricalBusinessObject {
    protected $id;
    protected $version;
    protected $type;
    protected $subType;
    protected $deleted;
    protected $auditTrail;
    protected $locks;
    
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
     * Create a new instance of the Business Object
     *
     * @var array $data     [Optional] Initial values for object properties
     * @var array $options  [Optional] Configuration options for the object
     * @return void
     */
    public function __construct($data = array(), $options = array()) {
        // Set defaults for new entities
        $this->version = 1;
        $this->deleted = 0;
        
        // Set values for valid properties
        if (count($data) > 0) {
            $this->populate($data);
        }
    }
    
    /**
     * Sets values for object properties, based on the inputs provided
     * 
     * @param EntityBusinessObject $data  May be an array or an instance of BusinessObject
     * @throws Tranquility\Exceptions\BusinessObjectException
     * @return Tranquility\Data\BusinessObjects\Entity
     */
    public function populate(Entity $entity) {
        $data = $entity->toArray();
        
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
        if (!($auditTrail instanceof \Tranquility\Data\BusinessObjects\Extensions\AuditTrail)) {
            throw new BusinessObjectException('Audit trail information must be provided as a \Tranquility\Data\BusinessObjects\Extensions\AuditTrail object');
        }
        
        $this->auditTrail = $auditTrail;
    }
    
    /**
     * Retrieve audit trail details for the entity as an array
     *
     * @return array
     */
    public function getAuditTrailDetails() {
        return $this->auditTrail->getAuditTrailDetails();
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
        $builder->setTable('history_entity');
        
        // Define inheritence
        $builder->setJoinedTableInheritance();
        $builder->setDiscriminatorColumn('type');
        $builder->addDiscriminatorMapClass('person', PersonHistoricalBusinessObject::class);
        $builder->addDiscriminatorMapClass('user', UserHistoricalBusinessObject::class);
        
        // Define fields
        $builder->createField('id', 'integer')->isPrimaryKey()->build();
        $builder->createField('version', 'integer')->isPrimaryKey()->build();
        $builder->addField('deleted', 'boolean');
        
        // Add relationships
        $builder->createOneToOne('auditTrail', AuditTrail::class)->addJoinColumn('transactionId','transactionId')->build();
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