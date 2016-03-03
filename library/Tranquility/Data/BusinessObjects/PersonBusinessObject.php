<?php namespace Tranquility\Data\BusinessObjects;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\ArrayCollection;

use Tranquility\Enums\System\EntityType                                     as EnumEntityType;
use Tranquility\Enums\BusinessObjects\Address\AddressTypes                  as EnumAddressType;
use Tranquility\Data\BusinessObjects\UserBusinessObject                     as User;
use Tranquility\Data\BusinessObjects\History\PersonHistoricalBusinessObject as PersonHistory;
use Tranquility\Exceptions\BusinessObjectException                          as BusinessObjectException;

class PersonBusinessObject extends EntityBusinessObject {
    use \Tranquility\Data\Traits\PropertyAccessorTrait;
    
    // Object properties
    protected $title;
    protected $firstName;
    protected $lastName;
    protected $position;
    
    // Related entities
    protected $user;
    
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
     * Array of properties that are mandatory when creating or updating a business object
     * 
     * @var array
     * @static
     */
    protected static $_mandatoryFields = array(
		'firstName',
        'lastName'
    );
    
    /**
     * Array of properties that are additionally mandatory only when creating a business object
     * 
     * @var array
     * @static
     */
    protected static $_mandatoryFieldsNewEntity = array();
    
    /**
     * Name of the class responsible for representing historical versions of this business entity
     * 
     * @var string
     * @static
     */
    protected static $_historicalEntityClass = PersonHistory::class;
    
    /** 
     * Type of entity represented by the business object
     *
     * @var string
     * @static
     */
    protected static $_entityType = EnumEntityType::Person;
    
    /**
     * Retrieve formatted name for person
     *
     * @return string
     */
    public function getFullName($includeTitle = false) {
        $name = $this->firstName.' '.$this->lastName;
        if ($includeTitle) {
            $name = $this->title.' '.$name;
        }
        return $name;
    }
    
    /**
     * Retrieve the User object associated with this person
     *
     * @return \Tranquility\Data\BusinessObjects\UserBusinessObject
     */
    public function getUserAccount() {
        return $this->user;
    }
    
    /**
     * Retreive a collection of addresses associated with this person
     *
     * @var string $type  Type of address collection to return
     * @return mixed
     */
    public function getAddresses($type) {
        // Build criteria to ensure we only retrieve active address records
        $addresses = array();
        $criteria = Criteria::create()->where(Criteria::expr()->eq("deleted", 0));
        switch ($type) {
            case EnumAddressType::Physical:
                $addresses = $this->physicalAddresses->matching($criteria);
                break;
            case EnumAddressType::Phone:
                $criteria = $criteria->orderBy(array("primaryContact" => Criteria::DESC));
                $addresses = $this->phoneAddresses->matching($criteria);
                break;
            case EnumAddressType::Electronic:
                $addresses = $this->electronicAddresses->matching($criteria);
                break;
            default:
                throw new BusinessObjectException('Invalid address type was supplied: '.$type);
        }
        return $addresses->toArray();
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
        $builder->setTable('entity_people');
        $builder->setCustomRepositoryClass('Tranquility\Data\Repositories\EntityRepository');
        
        // Define fields
        $builder->createField('title', 'string')->nullable()->build();
        $builder->addField('firstName', 'string');
        $builder->addField('lastName', 'string');
        $builder->addField('position', 'string');
        
        // Add relationships
        $builder->createOneToOne('user', User::class)->addJoinColumn('userId','id')->build();
    }
}