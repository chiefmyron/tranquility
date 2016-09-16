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
use Tranquility\Data\Objects\BusinessObjects\History\ContactHistoricalBusinessObject as ContactHistory;

// Tranquility related business objects
use Tranquility\Data\Objects\BusinessObjects\PersonBusinessObject                    as Person;
use Tranquility\Data\Objects\BusinessObjects\AccountBusinessObject                   as Account;

class ContactBusinessObject extends BusinessObject {
    use \Tranquility\Data\Objects\BusinessObjects\Traits\PropertyAccessorTrait;
    
    // Object properties
    protected $primaryContact;
    
    // Related entities
    protected $person;
    protected $account;
    
    /**
     * Array of properties that are specific to the Contact entity
     * 
     * @var array
     * @static
     */
    protected static $_fields = array(
        'primaryContact',
    );
    
    /**
     * Array of properties that are mandatory when creating or updating a Contact entity
     * 
     * @var array
     * @static
     */
    protected static $_mandatoryFields = array(
		'primaryContact',
    );
    
    /**
     * Array of properties that are additionally mandatory only when creating a new Contact entity
     * 
     * @var array
     * @static
     */
    protected static $_mandatoryFieldsNewEntity = array();
    
    /**
     * Array of properties that will not be displayed externally
     *
     * @static
     * @var array
     */
    protected static $_hiddenFields = array();
    
    /**
     * Name of the class responsible for representing historical versions of a AcContactcount entity
     * 
     * @var string
     * @static
     */
    protected static $_historicalEntityClass = ContactHistory::class;
    
    /** 
     * Type of entity represented by the business object
     *
     * @var string
     * @static
     */
    protected static $_entityType = EnumEntityType::Contact;
    
    /**
     * Metadata used to define object relationship to database
     *
     * @var \Doctrine\ORM\Mapping\ClassMetadata $metadata  Metadata to be passed to Doctrine
     * @return void
     */
    public static function loadMetadata(ClassMetadata $metadata) {
        $builder = new ClassMetadataBuilder($metadata);
        // Define table name
        $builder->setTable('entity_contacts');
        $builder->setCustomRepositoryClass('Tranquility\Data\Repositories\EntityRepository');
        
        // Define fields
        $builder->addField('primaryContact', 'boolean');
        
        // Add relationships
        $builder->createManyToOne('person', Person::class)->addJoinColumn('personId', 'id')->build();
        $builder->createManyToOne('account', Account::class)->addJoinColumn('accountId', 'id')->build();
    }
}