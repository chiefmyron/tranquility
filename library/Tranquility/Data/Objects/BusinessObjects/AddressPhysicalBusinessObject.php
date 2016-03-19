<?php namespace Tranquility\Data\Objects\BusinessObjects;

// Doctrine 2 libraries
use Doctrine\ORM\Mapping                                                                     as ORM;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\Common\Collections\ArrayCollection;

// Tranquility libraries
use Tranquility\Enums\System\EntityType                                                      as EnumEntityType;
use Tranquility\Exceptions\BusinessObjectException                                           as BusinessObjectException;

// Tranquility historical version of business object
use Tranquility\Data\Objects\BusinessObjects\History\AddressPhysicalHistoricalBusinessObject as AddressHistory;

// Tranquility related business objects
use Tranquility\Data\Objects\BusinessObjects\BusinessObject                                  as Entity;

class AddressPhysicalBusinessObject extends Entity {
    use \Tranquility\Data\Objects\BusinessObjects\Traits\PropertyAccessorTrait;
    
    protected $addressType;
    protected $addressLine1;
    protected $addressLine2;
    protected $addressLine3;
    protected $addressLine4;
    protected $city;
    protected $state;
    protected $postcode;
    protected $country;
    protected $latitude;
    protected $longitude;
    
    // Related entities
    protected $parentEntity;
    
    /**
     * Array of properties that are specific to the Physical Address entity
     * 
     * @var array
     * @static
     */
    protected static $_fields = array(
        'addressType',
        'addressLine1',
        'addressLine2',
        'addressLine3',
        'addressLine4',
        'city',
        'state',
        'postcode',
        'country',
        'latitude',
        'longitude'
    );
    
    /**
     * Array of properties that are mandatory when creating or updating a Physical Address entity
     * 
     * @var array
     * @static
     */
    protected static $_mandatoryFields = array(
		'addressType',
        'addressLine1',
        'city',
        'country',
    );
    
    /**
     * Array of properties that are additionally mandatory only when creating a new Physical Address entity
     * 
     * @var array
     * @static
     */
    protected static $_mandatoryFieldsNewEntity = array(
        'parent'
    );
    
    /**
     * Array of properties that will not be displayed externally
     *
     * @static
     * @var array
     */
    protected static $_hiddenFields = array();
    
    /**
     * Name of the class responsible for representing historical versions of this business entity
     * 
     * @var string
     * @static
     */
    protected static $_historicalEntityClass = AddressHistory::class;
    
    /** 
     * Type of entity represented by the business object
     *
     * @var string
     * @static
     */
    protected static $_entityType = EnumEntityType::AddressPhysical;
    
    /**
     * Sets values for object properties, based on the inputs provided
     * 
     * @param mixed $data  May be an array or an instance of BusinessObject
     * @throws Tranquility\Exceptions\BusinessObjectException
     * @return Tranquility\Data\Objects\BusinessObjects\BusinessObject
     */
    public function populate($data) {
        parent::populate($data);
        
        // If entity ID is not set, and parent object is present, set it now
        if (!isset($this->id) && isset($data['parent'])) {
            $this->parentEntity = $data['parent'];
        }
        
        return $this;
    }
    
    /**
     * Returns the parent entity for the address
     *
     * @return Tranquility\Data\Objects\BusinessObjects\BusinessObject
     */
    public function getParentEntity() {
        return $this->parentEntity;
    }
    
    /**
     * Retrieve an array containing geo-coordinates for the address
     *
     * @return string
     */
    public function getGeoCoordinates() {
        return array(
            'lat' => $this->latitude,
            'long' => $this->longitude
        );
    }
    
    /**
     * Get URL encoded, single line string of address
     *
     * @return string
     */
    public function urlEncodedAddress() {
        return urlencode($this->_joinAddressParts(","));
    }

    /*
     * Returns full address as a string, with each segment joined using 
     * the supplied separator character
     *
     * @param string $separator   String used to join address lines together
     * @return string
     */
    private function _joinAddressParts($separator) {
        $addressParts = array();
        if (isset($this->addressLine1) && $this->addressLine1 != '') {
            $addressParts[] = $this->addressLine1;
        }
        if (isset($this->addressLine2) && $this->addressLine2 != '') {
            $addressParts[] = $this->addressLine2;
        }
        if (isset($this->addressLine3) && $this->addressLine3 != '') {
            $addressParts[] = $this->addressLine3;
        }
        if (isset($this->addressLine4) && $this->addressLine4 != '') {
            $addressParts[] = $this->addressLine4;
        }

        $address_line_3 = trim($this->city.' '.$this->state.' '.$this->postcode);
        if ($address_line_3 != '') {
            $addressParts[] = $address_line_3;
        }
        if (isset($this->country) && $this->country != '') {
            $addressParts[] = $this->country;
        }

        // Glue address parts together and return
        return implode($separator, $addressParts);
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
        $builder->setTable('entity_addresses_physical');
        $builder->setCustomRepositoryClass('Tranquility\Data\Repositories\EntityRepository');
        
        // Define fields
        $builder->addField('addressType', 'string');
        $builder->addField('addressLine1', 'string');
        $builder->addField('addressLine2', 'string');
        $builder->addField('addressLine3', 'string');
        $builder->addField('addressLine4', 'string');
        $builder->addField('city', 'string');
        $builder->addField('state', 'string');
        $builder->addField('postcode', 'string');
        $builder->addField('country', 'string');
        $builder->addField('latitude', 'float');
        $builder->addField('longitude', 'float');
        
        // Add relationships
        $builder->createManyToOne('parentEntity', Entity::class)->addJoinColumn('parentId', 'id')->inversedBy('physicalAddresses')->build();
    }
}