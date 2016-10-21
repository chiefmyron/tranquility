<?php namespace Tranquility\Data\Repositories;

use Tranquility\Data\Objects\ExtensionObjects\Contact as Contact;

class PersonRepository extends EntityRepository {
    
    /**
     * Creates a new person record
     *
     * If an account is provided, the Person is associated as a contact
     * 
     * @param array $data  Input data to create the record
     * @return \Tranquility\Data\BusinessObjects\Entity
     */
    public function create(array $data) {
        // Create Person entity as normal
        $person = parent::create($data);

        if (isset($data['account'])) {
            // Create new contact record
            $contact = new Contact();
            $contact->setPerson($person);
            $contact->setAccount($data['account']);
            if (count($data['account']->getContacts()) <= 0) {
                $contact->primaryContact = true;
            } else {
                $contact->primaryContact = false;
            }
            $this->_em->persist($contact);
            $this->_em->flush();
        }

		// Return newly created entity
		return $person;
    }
    
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
        $historicalEntity->setAuditTrail($entity->getAuditTrail());
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
}