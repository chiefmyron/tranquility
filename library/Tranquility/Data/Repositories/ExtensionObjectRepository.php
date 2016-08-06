<?php namespace Tranquility\Data\Repositories;

use Doctrine\ORM\Tools\Pagination\Paginator;

use Tranquility\Data\BusinessObjects\Extensions\Tags       as Tag;
use Tranquility\Data\BusinessObjects\Extensions\AuditTrail as AuditTrail;

class ExtensionObjectRepository extends \Doctrine\ORM\EntityRepository {

    /**
     * Gets a full set of records
     */ 
    public function all() {
        // Start creation of query
        $entityName = $this->getEntityName();
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('e')->from($entityName, 'e');
        $query = $queryBuilder->getQuery();
        return $query->getResult();
    }

    /**
     * Creates a new record
     * 
     * @param array $data  Input data to create the record
     * @return mixed
     */
    public function create(array $data) {
        // Create new record
        $entityName = $this->getEntityName();
        $entity = new $entityName($data);
        $this->_em->persist($entity);
        $this->_em->flush();
		
		// Return newly created entity
		return $entity;
    }
    
    /**
     * Updates an existing record
     *
     * @param int   $id    Business object entity ID
     * @param array $data  Updated values to apply to the entity
     * @return mixed
     */ 
    public function update($id, array $data) {
        // Retrieve existing record
        $entity = $this->find($id);
        $entityName = $this->getEntityName();
        
        // Update existing record with new details
        $entity->populate($data);
        $this->_em->persist($entity);
        $this->_em->flush();
        
        // Return updated entity
        return $entity;
    }
}