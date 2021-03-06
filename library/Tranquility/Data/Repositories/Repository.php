<?php namespace Tranquility\Data\Repositories;

use \Tranquility\Utility as Utility;
use Illuminate\Support\Facades\Log;
use \Doctrine\ORM\Tools\Pagination\Paginator;

abstract class Repository extends \Doctrine\ORM\EntityRepository {

    // Enable pagination for collections
    use \LaravelDoctrine\ORM\Pagination\Paginatable;
    
    /**
     * Retrieve a set of all records
     *
     * Optional filter and pagination conditions can be specified for the result set
     *
     * @param array $filterConditions
     * @param array $orderConditions
     * @param int $resultsPerPage
     * @param int $startRecordIndex
     * @return array
     */
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
     * Creates a new record
     * 
     * @param array $data  Input data to create the record
     * @return mixed
     */
    abstract function create(array $data);
    
    /**
     * Updates an existing record
     *
     * @param int   $id    Record ID
     * @param array $data  Updated values to apply to the entity
     * @return mixed
     */ 
    abstract function update($id, array $data);
    
    /**
	 * Logically delete an existing record
	 *
	 * @param int   $id    Entity ID of the record to delete
	 * @param mixed
	 */
	abstract function delete($id, array $data);
    
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
        $parameterCounter = 0;
        
        // Add filter conditions
		foreach ($filterConditions as $filter) {
            // Get filter details
            $expression = null;
            $whereType = null;
            $fieldName = $filter[0];
            $operator = trim(strtoupper(Utility::extractValue($filter, 1, '=')));

            // Build expression for this filter
            switch ($operator) {
                case 'IS NULL':
                    $expression = $queryBuilder->expr()->isNull($fieldName);
                    $whereType = trim(strtoupper(Utility::extractValue($filter, 2, 'AND')));
                    break;
                case 'IS NOT NULL':
                    $expression = $queryBuilder->expr()->isNotNull($fieldName);
                    $whereType = trim(strtoupper(Utility::extractValue($filter, 2, 'AND')));
                    break;
                case 'IN':
                    $expression = $queryBuilder->expr()->in('e.'.$fieldName, '?'.$parameterCounter);
                    $parameters[$parameterCounter] = $filter[2];
                    $parameterCounter++;
                    $whereType = trim(strtoupper(Utility::extractValue($filter, 3, 'AND')));
                    break;
                case 'NOT IN':
                    $expression = $queryBuilder->expr()->notIn('e.'.$fieldName, '?'.$parameterCounter);
                    $parameters[$parameterCounter] = $filter[2];
                    $parameterCounter++;
                    $whereType = trim(strtoupper(Utility::extractValue($filter, 3, 'AND')));
                    break;
                case 'LIKE':
                    $expression = $queryBuilder->expr()->like('LOWER(e.'.$fieldName.')', '?'.$parameterCounter);
                    $parameters[$parameterCounter] = strtolower($filter[2]); // Case-insensitive searching
                    $parameterCounter++;
                    $whereType = trim(strtoupper(Utility::extractValue($filter, 3, 'AND')));
                    break;
                case 'NOT LIKE':
                    $expression = $queryBuilder->expr()->notLike('LOWER(e.'.$fieldName.')', '?'.$parameterCounter);
                    $parameters[$parameterCounter] = strtolower($filter[2]); // Case-insensitive searching
                    $parameterCounter++;
                    $whereType = trim(strtoupper(Utility::extractValue($filter, 3, 'AND')));
                    break;
            }

            // If we have an expression, add it to the query now
            if (!is_null($expression)) {
                // Existing expression
                if ($whereType == 'AND') {
                    $queryBuilder->andWhere($expression);
                } elseif ($whereType == 'OR') {
                    $queryBuilder->orWhere($expression);
                }
            } else {
                // Standard SQL comparision 
                $whereType = trim(strtoupper(Utility::extractValue($filter, 3, 'AND')));
                if ($whereType == 'AND') {
                    $queryBuilder = $queryBuilder->andWhere('e.'.$fieldName.' '.$filter[1].' ?'.$parameterCounter);
                } elseif ($whereType == 'OR') {
                    $queryBuilder = $queryBuilder->orWhere('e.'.$fieldName.' '.$filter[1].' ?'.$parameterCounter);
                }
                $parameters[$parameterCounter] = $filter[2];
                $parameterCounter++;
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

        // Log generated query
        Log::debug($queryBuilder->getQuery()->getSQL());
		
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