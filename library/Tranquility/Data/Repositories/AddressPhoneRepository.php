<?php namespace Tranquility\Data\Repositories;

use Doctrine\ORM\Tools\Pagination\Paginator;
use Tranquility\Data\BusinessObjects\Extensions\AuditTrail;

class AddressPhoneRepository extends EntityRepository {
    
    /**
     * Marks a phone address record as the primary contact mechanism. Any existing primary
     * phone records will have the flag removed.
     *
     * @param int   $id    ID for existing phone address record
     * @param array $data  Audit trail details
     * @return \Tranquility\Data\BusinessObjects\Entity
     */ 
    public function makePrimary($id, array $data) {
        // Retrieve existing record
        $entity = $this->find($id);
        $entityName = $this->getEntityName();
        $parentId = $entity->getParentEntity()->id;
        
        // Create new audit trail record
		$auditTrail = new AuditTrail($data);
        $this->_em->persist($auditTrail);
        
        // Get a list of any existing addresses that have the primary contact flag set
        $filters = array(['primaryContact', '=', '1']);
        $primaryContactAddresses = $this->all($filters);
        foreach ($primaryContactAddresses as $address) {
            // Create historical version of entity
            $historyClassName = call_user_func($entityName.'::getHistoricalEntityClass');
            $historicalEntity = new $historyClassName($address);
            $historicalEntity->setAuditTrail($address->getAuditTrailDetails());
            $this->_em->persist($historicalEntity);
            
            // Set primary contact flag to false
            $address->primaryContact = 0;
            $address->version = ($address->version + 1);
            $address->setAuditTrail($auditTrail);
            $this->_em->persist($address);
        }
        
        // Set address as the new primary contact
        $entity->primaryContact = 1;
        $entity->version = ($entity->version + 1);
        $entity->setAuditTrail($auditTrail);
        $this->_em->persist($entity);
        $this->_em->flush();
        
        // Return updated entity
        return $entity;
    }
}