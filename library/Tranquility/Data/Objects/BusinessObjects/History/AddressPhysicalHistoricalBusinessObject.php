<?php namespace Tranquility\Data\Objects\BusinessObjects\History;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\Common\Collections\ArrayCollection;

use Tranquility\Data\Objects\BusinessObjects\BusinessObject as Entity;

class AddressPhysicalHistoricalBusinessObject extends HistoricalBusinessObject {
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
     * Property definition for object
     * 
     * @static
     * @var array
     */
    protected static $_fieldDefinitions = array(
        'addressType'  => array('mandatoryUpdate', 'mandatoryCreate'),
        'addressLine1' => array('mandatoryUpdate', 'mandatoryCreate'),
        'addressLine2' => array(),
        'addressLine3' => array(),
        'addressLine4' => array(),
        'city'         => array('mandatoryCreate'),
        'state'        => array(),
        'postcode'     => array(),
        'country'      => array('mandatoryCreate'),
        'latitude'     => array(),
        'longitude'    => array()
    );
    
    /**
     * Sets values for object properties, based on the inputs provided
     * 
     * @param mixed $data  May be an array or an instance of BusinessObject
     * @throws Tranquility\Exceptions\BusinessObjectException
     * @return Tranquility\Data\BusinessObjects\Entity
     */
    public function populate($data) {
        parent::populate($data);
        
        // Get parent ID from original address record
        $this->parentEntity = $data->getParentEntity();
        return $this;
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
    
    public function getParentEntity() {
        return $this->parentEntity;
    }
    
    public function urlEncodedAddress() {
        return urlencode($this->_joinAddressParts(","));
    }

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
        $builder->setTable('history_entity_addresses_physical');
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