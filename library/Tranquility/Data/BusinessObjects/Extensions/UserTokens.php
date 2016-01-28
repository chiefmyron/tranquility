<?php namespace Tranquility\Data\BusinessObjects\Extensions;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\Common\Collections\ArrayCollection;

class UserTokens {
    protected $userId;
    protected $sessionId;
    protected $rememberToken;
    
    public static function loadMetadata(ClassMetadata $metadata) {
        $builder = new ClassMetadataBuilder($metadata);
        // Define table name
        $builder->setTable('sys_user_tokens');
        
        // Define fields
        $builder->createField('userId', 'integer')->isPrimaryKey()->build();
        $builder->addField('sessionId', 'string');
        $builder->addField('rememberToken', 'string');
    }
    
    public function getRememberToken() {
        return $this->rememberToken;
    }
    
    public function setRememberToken($token) {
        $this->rememberToken = $token;
    }
}