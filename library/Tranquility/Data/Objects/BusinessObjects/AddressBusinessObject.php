<?php namespace Tranquility\Data\Objects\BusinessObjects;

// Doctrine 2 libraries
use Doctrine\ORM\Mapping                                                             as ORM;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\Common\Collections\ArrayCollection;

// Tranquility libraries
use Tranquility\Enums\System\EntityType                                              as EnumEntityType;
use Tranquility\Exceptions\BusinessObjectException                                   as BusinessObjectException;

// Tranquility historical version of business object
use Tranquility\Data\Objects\BusinessObjects\History\AddressHistoricalBusinessObject as AddressHistory;

// Tranquility related business objects
use Tranquility\Data\Objects\BusinessObjects\BusinessObject                          as Entity;

class AddressBusinessObject extends Entity {
    use \Tranquility\Data\Objects\BusinessObjects\Traits\PropertyAccessorTrait;
    
    protected $category;
    protected $addressType;
    protected $addressText;
    protected $primaryContact;

    // Related entities
    protected $parentEntity;

    /**
     * Property definition for object
     * 
     * @static
     * @var array
     */
    protected static $_fieldDefinitions = array(
        'category'       => array('mandatoryUpdate', 'mandatoryCreate'),
        'addressType'    => array('mandatoryUpdate', 'mandatoryCreate'),
        'addressText'    => array('mandatoryUpdate', 'mandatoryCreate'),
        'primaryContact' => array(),
        'parent'         => array('mandatoryCreate')
    );
    
    /**
     * Name of the class responsible for representing historical versions of this business entity
     * 
     * @var string
     * @static
     */
    protected static $_historicalEntityClass = AddressHistory::class;
    
    /** 
     * Type of entity represented by the business object
     *
     * @var string
     * @static
     */
    protected static $_entityType = EnumEntityType::Address;
    
    /**
     * Sets values for object properties, based on the inputs provided
     * 
     * @param mixed $data  May be an array or an instance of BusinessObject
     * @throws Tranquility\Exceptions\BusinessObjectException
     * @return Tranquility\Data\Objects\BusinessObjects\BusinessObject
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
     * @return Tranquility\Data\Objects\BusinessObjects\BusinessObject
     */
    public function getParentEntity() {
        return $this->parentEntity;
    }
    
    public function toString() {
        return $this->addressText;
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
        $builder->setTable('entity_addresses');
        $builder->setCustomRepositoryClass('Tranquility\Data\Repositories\AddressRepository');
        
        // Define fields
        $builder->addField('category', 'string');
        $builder->addField('addressType', 'string');
        $builder->addField('addressText', 'string');
        $builder->addField('primaryContact', 'boolean');
        
        // Add relationships
        $builder->createManyToOne('parentEntity', Entity::class)->addJoinColumn('parentId', 'id')->inversedBy('addresses')->build();
    }
}