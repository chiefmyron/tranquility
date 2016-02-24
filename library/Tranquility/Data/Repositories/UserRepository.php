<?php namespace Tranquility\Data\Repositories;

use Doctrine\ORM\Tools\Pagination\Paginator;
use Tranquility\Data\BusinessObjects\Extensions\AuditTrail;

class UserRepository extends EntityRepository {
    
    /**
     * Updates an existing entity record, and moves the old version of the record
     * into a historical table
     *
     * Overridden to ensure that password is copied across to historical user record
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
        $historicalEntity->setAuthPassword($entity->getAuthPassword());
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
     * Update the "remember me" token for the given user in storage.
     *
     * @param  int     $id
     * @param  string  $token
     * @return void
     */
	public function updateRememberToken($id, $token) {
        // Load existing user record
        $user = $this->find($id);
        
        // Set new token
        $user->setRememberToken($token);
        $this->_em->persist($user);
        $this->_em->flush();
	}
    
    /**
	 * Retrieve a user by by their unique identifier and "remember me" token.
	 *
	 * @param  mixed   $id
	 * @param  string  $token
	 * @return \Tranquility\Data\BusinessObjects\User
	 */
    public function findByToken($id, $token) {
        // Start creation of query
        $queryString  = "SELECT u from ".$entityName." u ";
        $queryString .= "  JOIN u.userTokens t ";
        $queryString .= " WHERE u.id = :id ";
        $queryString .= "   AND u.deleted = 0 ";
        $queryString .= "   AND t.rememberToken = :token";
        $query = $this->_em->createQuery($queryString);
        return $query->getResult();
    }
    
    /**
	 * Retrieve all users that have Person records associated with them
	 *
	 * @param array $filter Used to specify additional filters to the set of results
	 * @param array $order Used to specify order parameters to the set of results
     * @param int $resultsPerPage If zero or less, or null, the full result set will be returned
	 * @param int $startRecordIndex Index of the record to start the result set from. Defaults to zero.
	 * @return \Tranquility\Service\ServiceResponse
	 */
    public function getPeopleWithUserAccounts($filterConditions = array(), $orderConditions = array(), $resultsPerPage = 0, $startRecordIndex = 0) {
        // Start creation of query
        $entityName = $this->getEntityName();
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('e')
                     ->from(\Tranquility\Data\BusinessObjects\UserBusinessObject::class, 'e')
                     ->innerJoin('e.person', 'person');
        
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
}