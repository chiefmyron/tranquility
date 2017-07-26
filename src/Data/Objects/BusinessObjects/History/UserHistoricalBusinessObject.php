<?php namespace Tranquility\Data\Objects\BusinessObjects\History;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\Common\Collections\ArrayCollection;

class UserHistoricalBusinessObject extends HistoricalBusinessObject  {
    use \Tranquility\Data\Objects\BusinessObjects\Traits\PropertyAccessorTrait;
    
    protected $username;
    protected $password;
    protected $timezoneCode;
    protected $localeCode;
    protected $active;
    protected $securityGroupId;
    protected $registeredDateTime;

        /**
     * Property definition for object
     * 
     * @static
     * @var array
     */
    protected static $_fieldDefinitions = array(
        'username'           => array('mandatoryUpdate', 'mandatoryCreate'),
        'timezoneCode'       => array('mandatoryUpdate', 'mandatoryCreate'),
        'localeCode'         => array('mandatoryUpdate', 'mandatoryCreate'),
        'active'             => array('mandatoryUpdate', 'mandatoryCreate'),
        'securityGroupId'    => array('mandatoryUpdate', 'mandatoryCreate'),
        'registeredDateTime' => array(),
    );
    
    /**
     * Set the password for the user.
     *
     * @param  string  $password
     * @return void
     */
    public function setAuthPassword($password) {
        $this->password = $password;
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
        $builder->setTable('history_entity_users');
        
        // Define fields
        $builder->addField('username', 'string');
        $builder->addField('password', 'string');
        $builder->addField('timezoneCode', 'string');
        $builder->addField('localeCode', 'string');
        $builder->addField('active', 'boolean');
        $builder->addField('securityGroupId', 'integer');
        $builder->addField('registeredDateTime', 'datetime');
    }
}