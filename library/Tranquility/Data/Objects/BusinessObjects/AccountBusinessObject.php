<?php namespace Tranquility\Data\Objects\BusinessObjects;

// Doctrine 2 libraries
use Doctrine\ORM\Mapping                                                             as ORM;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\ArrayCollection;

// Tranquility libraries
use Tranquility\Enums\System\EntityType                                              as EnumEntityType;
use Tranquility\Enums\BusinessObjects\Address\AddressTypes                           as EnumAddressType;
use Tranquility\Exceptions\BusinessObjectException                                   as BusinessObjectException;

// Tranquility historical version of business object
use Tranquility\Data\Objects\BusinessObjects\History\AccountHistoricalBusinessObject as AccountHistory;

// Tranquility related business objects
use Tranquility\Data\Objects\BusinessObjects\PersonBusinessObject                    as Person;

class AccountBusinessObject extends BusinessObject {
    use \Tranquility\Data\Objects\BusinessObjects\Traits\PropertyAccessorTrait;
    
    // Object properties
    protected $name;
    
    // Related entities
    protected $people;
    
    /**
     * Array of properties that are specific to the Account entity
     * 
     * @var array
     * @static
     */
    protected static $_fields = array(
        'name',
    );
    
    /**
     * Array of properties that are mandatory when creating or updating a Account entity
     * 
     * @var array
     * @static
     */
    protected static $_mandatoryFields = array(
		'name',
    );
    
    /**
     * Array of properties that are additionally mandatory only when creating a new Account entity
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
     * Name of the class responsible for representing historical versions of a Account entity
     * 
     * @var string
     * @static
     */
    protected static $_historicalEntityClass = AccountHistory::class;
    
    /** 
     * Type of entity represented by the business object
     *
     * @var string
     * @static
     */
    protected static $_entityType = EnumEntityType::Account;
    
    /**
     * Retreive a collection of addresses associated with this Account
     *
     * @var string $type  Type of address collection to return
     * @return mixed
     */
    public function getAddresses($type) {
        // Build criteria to ensure we only retrieve active address records
        $addresses = array();
        $criteria = Criteria::create()->where(Criteria::expr()->eq("deleted", 0));
        if ($type == EnumAddressType::Physical) {
            $addresses = $this->physicalAddresses->matching($criteria);
        } else {
            $criteria = $criteria->andWhere(Criteria::expr()->eq("category", $type))->orderBy(array("primaryContact" => Criteria::DESC));
            $addresses = $this->addresses->matching($criteria);
        }
        return $addresses->toArray();
    }

    /**
     * Retreive only the set of primary contact addresses
     *
     * @var string $type  Type of address collection to return
     * @return mixed
     */
    public function getPrimaryAddresses() {
        // Build criteria to ensure we only get the active and primary address records
        $criteria = Criteria::create()->where(Criteria::expr()->eq("deleted", 0));
        $criteria = $criteria->andWhere(Criteria::expr()->eq("primaryContact", true));
        $result = $this->addresses->matching($criteria);

        $addresses = array();
        foreach ($result as $address) {
            $addresses[$address->category] = $address;
        }

        return $addresses;
    }

    /**
     * Retreive the primary address for the spcified address type
     *
     * @var string $type  Type of address collection to return
     * @return mixed
     */
    public function getPrimaryAddress($type) {
        // Build criteria to ensure we only get the active and primary address records
        $criteria = Criteria::create()->where(Criteria::expr()->eq("deleted", 0));
        $criteria = $criteria->andWhere(Criteria::expr()->eq("primaryContact", true));
        $criteria = $criteria->andWhere(Criteria::expr()->eq("category", $type));
        $result = $this->addresses->matching($criteria);

        // If no primary address is set, return null
        if (count($result) <= 0) {
            return null;
        }

        return $result[0];
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
        $builder->setTable('entity_accounts');
        $builder->setCustomRepositoryClass('Tranquility\Data\Repositories\EntityRepository');
        
        // Define fields
        $builder->addField('name', 'string');
        
        // Add relationships
        $builder->createOneToOne('people', Person::class)->addJoinColumn('userId','id')->build();
    }
}