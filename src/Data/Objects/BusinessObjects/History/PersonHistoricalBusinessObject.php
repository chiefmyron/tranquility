<?php namespace Tranquility\Data\Objects\BusinessObjects\History;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\Common\Collections\ArrayCollection;

class PersonHistoricalBusinessObject extends HistoricalBusinessObject {
    use \Tranquility\Data\Objects\BusinessObjects\Traits\PropertyAccessorTrait;
    
    protected $title;
    protected $firstName;
    protected $lastName;
    protected $position;

    /**
     * Property definition for object
     * 
     * @static
     * @var array
     */
    protected static $_fieldDefinitions = array(
        'title'     => array(),
        'firstName' => array('mandatoryUpdate', 'mandatoryCreate', 'searchable'),
        'lastName'  => array('mandatoryUpdate', 'mandatoryCreate', 'searchable'),
        'position'  => array('searchable')
    );
    
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