<?php namespace Tranquility\Data\BusinessObjects\Extensions;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\Common\Collections\ArrayCollection;

use Tranquility\Data\BusinessObjects\EntityBusinessObject as Entity;

class Tags {
    protected $id;
    protected $text;
    protected $entities;
    
    public static function loadMetadata(ClassMetadata $metadata) {
        $builder = new ClassMetadataBuilder($metadata);
        // Define table name
        $builder->setTable('entity_tags');
        
        // Define fields
        $builder->createField('id', 'integer')->isPrimaryKey()->build();
        $builder->addField('text', 'string');
        
        // Define relationships
        $builder->createManyToMany('entities', Entity::class)->mappedBy('tags')->build();
    }
}