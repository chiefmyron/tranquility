<?php namespace Tranquility\Data\Repositories;

use Doctrine\ORM\Tools\Pagination\Paginator;

use Tranquility\Data\BusinessObjects\Extensions\Tags       as Tag;
use Tranquility\Data\BusinessObjects\Extensions\AuditTrail as AuditTrail;

class ExtensionObjectRepository extends \Doctrine\ORM\EntityRepository {
    
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
    
    /**
	 * Logically delete an existing record
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