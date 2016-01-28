<?php namespace Tranquility\Data\Repositories;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

class Entity extends EntityRepository {
    
    public function all($filterConditions = array(), $orderConditions = array(), $resultsPerPage = 0, $startRecordIndex = 0) {
        // Start creation of query
        $entityName = $this->getEntityName();
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('e')->from($entityName, 'e');
        
        // Add other filter conditions
        $queryBuilder = $this->_addQueryFilters($queryBuilder, $filterConditions, $orderConditions);
        
        // If pagination options have been supplied, add paging conditions
        $query = $queryBuilder->getQuery();
        if ($resultsPerPage > 0) {
            $query->setFirstResult($resultsPerPage)->setMaxResults($startRecordIndex);
            return new Paginator($queryBuilder);
        } else {
            return $query->getResult();
        }
    }
    
    /**
     * Creates a new entity record
     * 
     * @param array $data  Input data to create the record
     * @return \Tranquility\Data\BusinessObjects\Entity
     */
    public function create(array $data) {
		// Create new audit trail record
		$auditTrail = new \Tranquility\Data\BusinessObjects\Extensions\AuditTrail($data);
        $this->_em->persist($auditTrail);
        
        // Create new entity record, with the audit trail attached
        $entityName = $this->getEntityName();
        $entity = new $entityName($data);
        $entity->version = 1; // Force version for new records to be 1
        $entity->setAuditTrail($auditTrail);
        $this->_em->persist($entity);
        $this->_em->flush();
		
		// Return newly created entity
		return $entity;
    }
    
    /**
     * Updates an existing entity record, and moves the old version of the record
     * into a historical table
     *
     * @param int   $id    Business object entity ID
     * @param array $data  Updated values to apply to the entity
     * @return \Tranquility\Data\BusinessObjects\Entity
     */ 
    public function update($id, array $data) {
        // Retrieve existing record
        $entity = $this->find($id);
        $entityName = $this->getEntityName();
        
        // Create historical version of entity
        $historyClassName = call_user_func($entityName.'::getHistoricalEntityClass');
        $historicalEntity = new $historyClassName($entity);
        $historicalEntity->setAuditTrail($entity->getAuditTrailDetails());
        $this->_em->persist($historicalEntity);
        
        // Create new audit trail record
		$auditTrail = new \Tranquility\Data\BusinessObjects\Extensions\AuditTrail($data);
        $this->_em->persist($auditTrail);
        
        // Update existing entity record with new details, incremented version number
        // and new audit trail details
        unset($data['version']);  // Ensure passed data does not override internal versioning
        $entity->populate($data);
        $entity->version = ($entity->version + 1);
        $entity->setAuditTrail($auditTrail);
        $this->_em->persist($entity);
        $this->_em->flush();
        
        // Return updated entity
        return $entity;
    }
    
    /**
	 * Logically delete an existing entity record
	 *
	 * @param int   $id    Entity ID of the record to delete
	 * @param array 
	 */
	public function delete($id, array $data) {
        // Add deleted flag to data array
        $data['deleted'] = 1;
        return $this->update($id, $data);
	}
    
    /**
	 * Used to add additional query conditions, ordering and set limits to a selection query
	 *
	 * @param \Doctrine\ORM\QueryBuilder $query   The initial selection query
	 * @param array  $filterConditions            Array of filter conditions to append to the selection query
	 * @param array  $orderConditions             Array of order conditions to append to the selection query
	 * @return \Doctrine\ORM\QueryBuilder
	 */
	protected function _addQueryFilters($queryBuilder, $filterConditions = array(), $orderConditions = array()) {
		$parameters = array();
        
        // Add filter conditions
		foreach ($filterConditions as $filter) {
            $queryBuilder = $queryBuilder->andWhere('e.'.$filter[0].' '.$filter[1].' :'.$filter[0]);
            $parameters[$filter[0]] = $filter[2];
		}
		
		// Add order statements
		foreach ($orderConditions as $order) {
            $queryBuilder = $queryBuilder->addOrderBy('e.'.$order[0], $order[1]);
		} 
        
        // Add parameters
        foreach ($parameters as $key => $value) {
            $queryBuilder = $queryBuilder->setParameter($key, $value);
        }
		
		return $queryBuilder;
	}
    
    /**
	 * Checks for the existence of a particular field in the list of filter conditions
	 *
	 * @param array   $filterConditions The array of filter conditions that will be passed to the model
	 * @param string  $searchTerm       The field name to search for in the list of filter conditions
	 * @return mixed                    If search term is found, return the array containing that filter condition. Otherwise false.
	 */  
	protected function _checkForFilterCondition(array $filterConditions, $searchTerm) {
		foreach ($filterConditions as $filter) {
			if ($filter[0] == $searchTerm) {
				return $filter;
			}
		}
		
		return false;
	}	
}