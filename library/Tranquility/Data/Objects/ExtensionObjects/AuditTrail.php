<?php namespace Tranquility\Data\Objects\ExtensionObjects;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\Common\Collections\ArrayCollection;

use Tranquility\Data\Objects\BusinessObjects\UserBusinessObject;
use Tranquility\Exceptions\BusinessObjectException;

class AuditTrail extends ExtensionObject {
    use \Tranquility\Data\Objects\ExtensionObjects\Traits\PropertyAccessorTrait;
    
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
    
    public function getAuditTrailDetails() {
        return toArray();
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
        $builder->createOneToOne('updateBy', UserBusinessObject::class)->addJoinColumn('updateBy','id')->build();
    }
}