<?php namespace Tranquility\Data\Repositories;

use Doctrine\ORM\Tools\Pagination\Paginator;

use Tranquility\Data\Objects\ExtensionObjects\Tags       as Tag;
use Tranquility\Data\Objects\ExtensionObjects\AuditTrail as AuditTrail;

class EntityRepository extends \Doctrine\ORM\EntityRepository {

    // Enable pagination for entity collections
    use \LaravelDoctrine\ORM\Pagination\Paginatable;
    
    /**
     * Finds entities by a set of criteria.
     *
     * @param array      $criteria
     * @param array|null $orderBy
     * @param int|null   $limit
     * @param int|null   $offset
     *
     * @return array The objects.
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null) {
        // By default, do not find deleted records
        if (!isset($criteria['deleted'])) {
            $criteria['deleted'] = 0;
        }
        
        return parent::findBy($criteria, $orderBy, $limit, $offset);
    }
    
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
            // Paginate result set
            $resultSet = $this->paginate($query, $resultsPerPage);
            return $resultSet;
        } else {
            // Return entire result set
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
		$auditTrail = new AuditTrail($data);
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
        $historicalEntity->setAuditTrail($entity->getAuditTrail());
        $this->_em->persist($historicalEntity);
        
        // Create new audit trail record
		$auditTrail = new AuditTrail($data);
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
    
    public function addTag($id, $tag) {
        // Retrieve existing record
        $entity = $this->find($id);
        $entity->addTag($tag);
        $this->_em->flush();
        
        // Return updated entity
        return $entity;
    }
    
    public function removeTag($id, $tag) {
        // Retrieve existing record
        $entity = $this->find($id);
        $entity->removeTag($tag);
        $this->_em->flush();
        
        // Return updated entity
        return $entity;
    }
    
    public function setTags($id, array $tagCollection) {
        // Retrieve existing record
        $entity = $this->find($id);
        
        // Get existing tag collection for entity
        $existingTags = $entity->getTags();
        
        // Determine which tags need to be added
        $adds = array_diff($tagCollection, $existingTags);
        foreach($adds as $addTag) {
            $entity = $this->addTag($id, $addTag);
        }
        
        // Determine which tags need to be removed from the collection
        $removes = array_diff($existingTags, $tagCollection);
        foreach ($removes as $removeTag) {
            $entity = $this->removeTag($id, $removeTag);
        }
        
        // Return updated entity
        return $entity;
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
            // Check for specialised conditions
            if ((count($filter) == 2) && trim(strtoupper($filter[1]) == 'IS NULL')) {
                // Check for null
                $queryBuilder = $queryBuilder->add('where', $queryBuilder->expr()->isNotNull($filter[0]));
            } elseif ((count($filter) == 2) && trim(strtoupper($filter[1]) == 'IS NOT NULL')) {
                // Check for not null
                $queryBuilder = $queryBuilder->add('where', $queryBuilder->expr()->isNull($filter[0]));
            } elseif ((count($filter) == 3) && trim(strtoupper($filter[1]) == 'IN')) {
                // Check for values in array
                $queryBuilder = $queryBuilder->add('where', $queryBuilder->expr()->in('e.'.$filter[0], ':'.$filter[0]));
                $parameters[$filter[0]] = $filter[2];
            } elseif ((count($filter) == 3) && trim(strtoupper($filter[1]) == 'NOT IN')) {
                // Check for values not in array
                $queryBuilder = $queryBuilder->add('where', $queryBuilder->expr()->notIn('e.'.$filter[0], ':'.$filter[0]));
                $parameters[$filter[0]] = $filter[2];
            } elseif ((count($filter) == 3) && trim(strtoupper($filter[1]) == 'LIKE')) {
                // String search
                $queryBuilder = $queryBuilder->add('where', $queryBuilder->expr()->like('e.'.$filter[0], $queryBuilder->expr()->literal('%'.$filter[2].'%')));
                //$parameters[$filter[0]] = 
            } else {
                // Standard where clause
                $queryBuilder = $queryBuilder->andWhere('e.'.$filter[0].' '.$filter[1].' :'.$filter[0]);
                $parameters[$filter[0]] = $filter[2];
            }
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