<?php namespace Tranquility\Data\BusinessObjects\History;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\Common\Collections\ArrayCollection;

use Tranquility\Data\BusinessObjects\EntityBusinessObject as Entity;

class AddressHistoricalBusinessObject extends EntityHistoricalBusinessObject {
    use \Tranquility\Data\Traits\PropertyAccessorTrait;
    
    protected $category;
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
        'category',
        'addressType',
        'addressText',
        'primaryContact',
    );
    
    /**
     * Sets values for object properties, based on the inputs provided
     * 
     * @param mixed $data  May be an array or an instance of BusinessObject
     * @throws Tranquility\Exceptions\BusinessObjectException
     * @return Tranquility\Data\BusinessObjects\Entity
     */
    public function populate($data) {
        parent::populate($data);
        
        // Get parent ID from original address record
        $this->parentEntity = $data->getParentEntity();
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
        $builder->setTable('history_entity_addresses');
        $builder->setCustomRepositoryClass('Tranquility\Data\Repositories\EntityRepository');
        
        // Define fields
        $builder->addField('category', 'string');
        $builder->addField('addressType', 'string');
        $builder->addField('addressText', 'string');
        $builder->addField('primaryContact', 'boolean');
        
        // Add relationships
        $builder->createManyToOne('parentEntity', Entity::class)->addJoinColumn('parentId', 'id')->inversedBy('addresses')->build();
    }
}