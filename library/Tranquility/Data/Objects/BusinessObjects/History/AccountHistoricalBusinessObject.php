<?php namespace Tranquility\Data\Objects\BusinessObjects\History;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\Common\Collections\ArrayCollection;

class AccountHistoricalBusinessObject extends HistoricalBusinessObject {
    use \Tranquility\Data\Objects\BusinessObjects\Traits\PropertyAccessorTrait;
    
    protected $name;
    
    /**
     * Array of properties that are specific to a business object of a particular entity type
     * 
     * @var array
     * @static
     */
    protected static $_fields = array(
        'name'
    );
    
    /**
     * Metadata used to define object relationship to database
     *
     * @var \Doctrine\ORM\Mapping\ClassMetadata $metadata  Metadata to be passed to Doctrine
     * @return void
     */
    public static function loadMetadata(ClassMetadata $metadata) {
        $builder = new ClassMetadataBuilder($metadata);
        // Define table name
        $builder->setTable('history_entity_accounts');
        $builder->setCustomRepositoryClass('Tranquility\Data\Repositories\Entity');
        
        // Define fields
        $builder->addField('name', 'string');
    }
}