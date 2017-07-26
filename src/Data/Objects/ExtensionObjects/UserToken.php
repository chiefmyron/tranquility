<?php namespace Tranquility\Data\Objects\ExtensionObjects;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\Common\Collections\ArrayCollection;

use Tranquility\Data\Objects\BusinessObjects\UserBusinessObject as User;
use Tranquility\Enums\System\UserTokenType as EnumTokenType;

class UserToken extends ExtensionObject {
    use \Tranquility\Data\Objects\ExtensionObjects\Traits\PropertyAccessorTrait;
    
    protected $user;
    protected $type;
    protected $tokenText;
    
    /**
     * List of properties that can be accessed via getters and setters
     * 
     * @static
     * @var array
     */
    protected static $_fields = array(
        'user',
        'type',
        'tokenText',
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
        $builder->setTable('sys_user_tokens');
        
        // Define fields
        $builder->createField('type', 'string')->isPrimaryKey()->build();
        $builder->addField('tokenText', 'string');
        
        // Add relationships
        $builder->createManyToOne('user', User::class)->addJoinColumn('userId', 'id')->inversedBy('userTokens')->build();
    }
    
    /**
	 * Get the token value.
	 *
	 * @return string
	 */
    public function getToken() {
        return $this->tokenText;
    }
    
    /**
	 * Set the token value.
	 *
	 * @param  string  $value
	 * @return UserToken
	 */
    public function setToken($type, $value) {
        if (!EnumTokentype::isValidValue($type)) {
            throw new Exception('Invalid user token type supplied: '.$type);
        }
        $this->type = $type;
        $this->tokenText = $value;
        return $this;
    }
    
    /**
     * Get the token type.
     *
     * @return string
     */
    public function getTokenType() {
        return $this->type;
    }
}