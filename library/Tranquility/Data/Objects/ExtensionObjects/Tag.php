<?php namespace Tranquility\Data\Objects\ExtensionObjects;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\Common\Collections\ArrayCollection;

use Tranquility\Data\Objects\BusinessObjects\BusinessObject as Entity;

class Tag extends ExtensionObject {
    use \Tranquility\Data\Objects\ExtensionObjects\Traits\PropertyAccessorTrait;
    
    /**
     * List of properties that can be accessed via getters and setters
     * 
     * @static
     * @var array
     */
    protected static $_fields = array(
        'id',
        'text'
    );
    
    /**
     * Array of common properties that all Business Objects will require
     * when creating or updating
     *
     * @static
     * @var array
     */
    protected static $_mandatoryFields = array(
        'text'
    );
    
    protected $id;
    protected $text;
    protected $entities;
    
    /**
     * Create a new instance of the Tag
     *
     * @var array $data     [Optional] Initial values for object properties
     * @var array $options  [Optional] Configuration options for the object
     * @return void
     */
    public function __construct($data = array(), $options = array()) {
        parent::__construct($data, $options);
        
        // Initialise collections for related entities
        $this->entities = new ArrayCollection();
    }
    
    /**
     * Returns a list of all available fields for the business object
     *
     * @static
     * @return array
     */
    public static function getExtensionFields() {
        return self::$_fields;
    }
    
    /**
     * Returns a list of fields required to create or update a new business object
     *
     * @static
     * @var boolean $newRecord  Adjusts the set of mandatory fields based on whether a record is being created or updated
     * @return array
     */
    public static function getMandatoryExtensionFields($newRecord = false) {
        if (!$newRecord) {
            // ID will be mandatory for any updates to records
            $mandatoryFields = self::$_mandatoryFields;
            array_unshift($mandatoryFields, 'id');
            return $mandatoryFields;
        }
        return self::$_mandatoryFields;
    }
    
    /**
     * Retreive a collection of business object entities associated with this tag
     *
     * @return array
     */
    public function getRelatedEntities() {
        return $this->entities->toArray();
    }
    
    /**
     * Remove an entity from the collection of related entities for the tag
     *
     * @param Tranquility\Data\BusinessObjects\Entity $entity   Existing entity to remove from the collection of related entities
     * @return Tranquility\Data\BusinessObjects\Extensions\Tags
     */
    public function removeRelatedEntity(Entity $entity) {
        if ($this->entities->contains($entity) === false) {
            return $this;
        }
        $this->entities->removeElement($entity);
        return $this;
    }
    
    /**
     * Add a new entity to the collection of related entities for the tag
     *
     * @param Tranquility\Data\BusinessObjects\Entity $entity   New entity to associated with the tag
     * @return Tranquility\Data\BusinessObjects\Extensions\Tags
     */
    public function addRelatedEntity(Entity $entity) {
        if ($this->entities->contains($entity) === true) {
            return $this;
        }
        
        $this->entities->add($entity);
        return $this;
    }
    
    public function __toString() {
        return $this->text;
    }
    
    public static function loadMetadata(ClassMetadata $metadata) {
        $builder = new ClassMetadataBuilder($metadata);
        // Define table name
        $builder->setTable('ext_tags');
        $builder->setCustomRepositoryClass('Tranquility\Data\Repositories\ExtensionObjectRepository');
        
        // Define fields
        $builder->createField('id', 'integer')->isPrimaryKey()->generatedValue()->build();
        $builder->addField('text', 'string');
        
        // Define relationships
        $builder->createManyToMany('entities', Entity::class)->mappedBy('tags')->build();
    }
}