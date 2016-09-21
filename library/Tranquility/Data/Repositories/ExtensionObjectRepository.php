<?php namespace Tranquility\Data\Repositories;

class ExtensionObjectRepository extends Repository {
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
	 * Logically delete an existing entity record
	 *
	 * @param int   $id    Entity ID of the record to delete
	 * @param array 
	 */
	public function delete($id, array $data) {
        return parent::delete($id, $data);
	}
}