<?php namespace Tranquility\Data\Objects\BusinessObjects;

// Doctrine 2 libraries
use Doctrine\ORM\Mapping                                                             as ORM;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\ArrayCollection;

// Tranquility libraries
use Tranquility\Enums\System\EntityType                                              as EnumEntityType;
use Tranquility\Exceptions\BusinessObjectException                                   as BusinessObjectException;

// Tranquility historical version of business object
use Tranquility\Data\Objects\BusinessObjects\History\ProductHistoricalBusinessObject as ProductHistory;

abstract class ProductBusinessObject extends BusinessObject {
    use \Tranquility\Data\Objects\BusinessObjects\Traits\PropertyAccessorTrait;
    
    // Object properties
    protected $sku;
    protected $name;
    protected $description;
    protected $price;
    protected $status;
    
    /** 
     * Type of entity represented by the business object
     *
     * @var string
     * @static
     */
    protected static $_entityType = EnumEntityType::Product;

    /**
     * Name of the class responsible for representing historical versions of a Product entity
     * 
     * @var string
     * @static
     */
    protected static $_historicalEntityClass = ProductHistory::class;

    /**
     * Property definition for object
     * 
     * @static
     * @var array
     */
    protected static $_fieldDefinitions = array(
        'sku' => array('mandatoryUpdate', 'mandatoryCreate', 'searchable'),
        'name' => array('mandatoryUpdate', 'mandatoryCreate', 'searchable'),
        'description' => array('searchable'),
        'price' => array('mandatoryUpdate', 'mandatoryCreate')
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
        $builder->setTable('entity_accounts');
        $builder->setCustomRepositoryClass('Tranquility\Data\Repositories\EntityRepository');
        
        // Define fields
        $builder->addField('name', 'string');
        
        // Add relationships
        $builder->createOneToMany('contacts', Contact::class)->mappedBy('account')->orphanRemoval(true)->cascadePersist()->cascadeRemove()->build();
    }

    /**
     * Create a new instance of the Account
     *
     * @var array $data     [Optional] Initial values for object properties
     * @var array $options  [Optional] Configuration options for the object
     * @return void
     */
    public function __construct($data = array(), $options = array()) {
        parent::__construct($data, $options);
        
        // Initialise collections for related entities
        $this->contacts = new ArrayCollection();
    }

    //*************************************************************************
    // Class-specific getter and setter methods                               *
    //*************************************************************************

}