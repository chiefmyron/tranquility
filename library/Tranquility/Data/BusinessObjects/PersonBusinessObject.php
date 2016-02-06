<?php namespace Tranquility\Data\BusinessObjects;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\Common\Collections\ArrayCollection;

use Tranquility\Data\BusinessObjects\UserBusinessObject                     as User;
use Tranquility\Data\BusinessObjects\History\PersonHistoricalBusinessObject as PersonHistory;

class PersonBusinessObject extends EntityBusinessObject {
    use \Tranquility\Data\Traits\PropertyAccessorTrait;
    
    protected $title;
    protected $firstName;
    protected $lastName;
    protected $position;
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
    
    public function getUserAccount() {
        return $this->user;
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
        $builder->addField('title', 'string');
        $builder->addField('firstName', 'string');
        $builder->addField('lastName', 'string');
        $builder->addField('position', 'string');
        
        // Add relationships
        $builder->createOneToOne('user', User::class)->addJoinColumn('userId','id')->build();
    }
}