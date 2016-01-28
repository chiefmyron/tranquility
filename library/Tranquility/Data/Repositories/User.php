<?php namespace Tranquility\Data\Repositories;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

class User extends Entity {
    
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
}