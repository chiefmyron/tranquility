<?php namespace Tranquility\Data\BusinessObjects\History;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\Common\Collections\ArrayCollection;

class PersonHistoricalBusinessObject extends EntityHistoricalBusinessObject {
    use \Tranquility\Data\Traits\PropertyAccessorTrait;
    
    protected $title;
    protected $firstName;
    protected $lastName;
    protected $position;
    
    /**
     * Array of properties that are specific to a business object of a particular entity type
     * 
     * @var array
     * @static
     */
    protected static $_fields = array(
        'title',
        'firstName',
        'lastName',
        'position'
    );
    
    /**
     * Name of the class responsible for representing historical versions of this business entity
     * 
     * @var string
     * @static
     */
    protected static $_historicalEntityClass = PersonHistory::class;
    
    /**
     * Retrieve formatted name for person
     *
     * @return string
     */
    public function getFullName() {
        return $this->firstName.' '.$this->lastName;
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
        $builder->setTable('history_entity_people');
        $builder->setCustomRepositoryClass('Tranquility\Data\Repositories\Entity');
        
        // Define fields
        $builder->addField('title', 'string');
        $builder->addField('firstName', 'string');
        $builder->addField('lastName', 'string');
        $builder->addField('position', 'string');
    }
}