<?php namespace Tranquility\Data\BusinessObjects;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\Common\Collections\ArrayCollection;

use Tranquility\Enums\System\EntityType                                           as EnumEntityType;
use Tranquility\Data\BusinessObjects\EntityBusinessObject                         as Entity;
use Tranquility\Data\BusinessObjects\History\AddressPhoneHistoricalBusinessObject as AddressPhoneHistory;

class AddressPhoneBusinessObject extends EntityBusinessObject {
    use \Tranquility\Data\Traits\PropertyAccessorTrait;
    
    protected $addressType;
    protected $addressText;
    protected $primaryContact;

    // Related entities
    protected $parentEntity;
    
    /**
     * Array of properties that are specific to a business object of a particular entity type
     * 
     * @var array
     * @static
     */
    protected static $_fields = array(
        'addressType',
        'addressText',
        'primaryContact',
    );
    
    /**
     * Array of properties that are mandatory when creating or updating a business object
     * 
     * @var array
     * @static
     */
    protected static $_mandatoryFields = array(
		'addressType',
        'addressText',
    );
    
    /**
     * Array of properties that are additionally mandatory only when creating a business object
     * 
     * @var array
     * @static
     */
    protected static $_mandatoryFieldsNewEntity = array(
        'parent'
    );
    
    /**
     * Name of the class responsible for representing historical versions of this business entity
     * 
     * @var string
     * @static
     */
    protected static $_historicalEntityClass = AddressPhoneHistory::class;
    
    /** 
     * Type of entity represented by the business object
     *
     * @var string
     * @static
     */
    protected static $_entityType = EnumEntityType::AddressPhone;
    
    /**
     * Sets values for object properties, based on the inputs provided
     * 
     * @param mixed $data  May be an array or an instance of BusinessObject
     * @throws Tranquility\Exceptions\BusinessObjectException
     * @return Tranquility\Data\BusinessObjects\Entity
     */
    public function populate($data) {
        parent::populate($data);
        
        // If entity ID is not set, and parent object is present, set it now
        if (!isset($this->id) && isset($data['parent'])) {
            $this->parentEntity = $data['parent'];
        }
        
        return $this;
    }
    
    /**
     * Returns the parent entity for the address
     *
     * @return Tranquility\Data\BusinessIbjects\Entity
     */
    public function getParentEntity() {
        return $this->parentEntity;
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
        $builder->setTable('entity_addresses_phone');
        $builder->setCustomRepositoryClass('Tranquility\Data\Repositories\EntityRepository');
        
        // Define fields
        $builder->addField('addressType', 'string');
        $builder->addField('addressText', 'string');
        $builder->addField('primaryContact', 'boolean');
        
        // Add relationships
        $builder->createManyToOne('parentEntity', Entity::class)->addJoinColumn('parentId', 'id')->inversedBy('phoneAddresses')->build();
    }
}