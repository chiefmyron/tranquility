<?php namespace Tranquility\Data\Objects\ExtensionObjects;

// Doctrine 2 libraries
use Doctrine\ORM\Mapping                                                             as ORM;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\ArrayCollection;

// Tranquility libraries
use Tranquility\Enums\System\EntityType                                              as EnumEntityType;
use Tranquility\Exceptions\BusinessObjectException                                   as BusinessObjectException;

// Tranquility related business objects
use Tranquility\Data\Objects\BusinessObjects\PersonBusinessObject                    as Person;
use Tranquility\Data\Objects\BusinessObjects\AccountBusinessObject                   as Account;

class Contact extends ExtensionObject {
    use \Tranquility\Data\Objects\ExtensionObjects\Traits\PropertyAccessorTrait;
    
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
     * Type of entity represented by the business object
     *
     * @var string
     * @static
     */
    protected static $_entityType = EnumEntityType::Contact;

    public function getPerson() {
        return $this->person;
    }

    public function getAccount() {
        return $this->account;
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
        $builder->setTable('entity_contacts');
        $builder->setCustomRepositoryClass('Tranquility\Data\Repositories\ExtensionObjectRepository');
        
        // Define fields
        $builder->addField('primaryContact', 'boolean');
        
        // Add relationships
        $builder->createManyToOne('account', Account::class)->makePrimaryKey()->addJoinColumn('accountId', 'id')->inversedBy('contacts')->build();
        $builder->createManyToOne('person', Person::class)->makePrimaryKey()->addJoinColumn('personId', 'id')->inversedBy('contacts')->build();
    }
}