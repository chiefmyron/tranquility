<?php namespace Tranquility\Data\BusinessObjects\Extensions;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\Common\Collections\ArrayCollection;

use Tranquility\Data\BusinessObjects\User as User;

class AuditTrail {
    protected $transactionId;
    protected $transactionSource;
    protected $updateBy;
    protected $updateDateTime;
    protected $updateReason;
    
    /**
     * List of properties that can be accessed via getters and setters
     * 
     * @static
     * @var array
     */
    protected static $_fields = array(
        'transactionId',
        'transactionSource',
        'updateBy',
        'updateDateTime',
        'updateReason'
    );
    
    /**
     * Array of common properties that all Business Objects will require
     * when creating or updating
     *
     * @static
     * @var array
     */
    protected static $_mandatoryFields = array(
        'transactionSource',
        'updateBy',
        'updateDateTime',
        'updateReason',
    );
    
    public function __construct($data = array()) {
        $this->populate($data);   
    }
    
    /**
     * Sets values for object properties, based on the inputs provided
     * 
     * @param mixed $data  May be an array or an instance of BusinessObject
     * @throws Tranquility\Data\Exceptions\BusinessObjectException
     * @return Tranquility\Data\BusinessObjects\Entity
     */
    public function populate($data) {
        if ($data instanceof AuditTrail) {
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
    
    public function getAuditTrailDetails() {
        return toArray();
    }
    
    public function toArray() {
        return array(
            'transactionId' => $this->transactionId,
            'transactionSource' => $this->transactionSource,
            'updateBy' => $this->updateBy,
            'updateDateTime' => $this->updateDateTime,
            'updateReason' => $this->updateReason    
        );
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
        $builder->setTable('sys_trans_audit');
        
        // Define fields
        $builder->createField('transactionId', 'integer')->isPrimaryKey()->generatedValue()->build();
        $builder->addField('transactionSource', 'string');
        $builder->addField('updateDateTime', 'datetime');
        $builder->addField('updateReason', 'string');
        
        // Add relationships
        $builder->createOneToOne('updateBy', User::class)->addJoinColumn('updateBy','id')->build();
    }
    
    /**
     * Returns a list of all available fields for the audit trail
     *
     * @static
     * @return array
     */
    public static function getFields() {
        return self::$_fields;
    }
    
    /**
     * Returns a list of fields required to create a new audit trail record
     *
     * @static
     * @return array
     */
    public static function getMandatoryFields() {
        return self::$_mandatoryFields;
    }
}