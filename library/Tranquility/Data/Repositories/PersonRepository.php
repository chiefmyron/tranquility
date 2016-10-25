<?php namespace Tranquility\Data\Repositories;

use Tranquility\Utility as Utility;
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
     * @param int   $id    Business object entity ID
     * @param array $data  Updated values to apply to the entity
     * @return \Tranquility\Data\BusinessObjects\Entity
     */ 
    public function update($id, array $data) {
        // Update Person entity as normal
        $person = parent::update($id, $data);

        // Get associated Account details
        $account = Utility::extractValue($data, 'account', null);
        $contact = $person->getContact();

        // Check if we need to remove an existing Contact record
        if (is_null($account) && !is_null($contact)) {
            $person->removeContact($contact);
            $this->_em->persist($person);
            $this->_em->flush();
        }

        // Check if we need to create a new Contact record
        if (!is_null($account) && is_null($contact)) {
            $contact = new Contact();
            $contact->setPerson($person);
            $contact->setAccount($account);
            if (count($account->getContacts()) <= 0) {
                $contact->primaryContact = true;
            } else {
                $contact->primaryContact = false;
            }
            $this->_em->persist($contact);
            $this->_em->flush();
        }

        // Check if we need to update an existing Contact record
        if (!is_null($account) && !is_null($contact) && $person->getAccount() != $account) {
            $contact->setAccount($account);
            if (count($account->getContacts()) <= 0) {
                $contact->primaryContact = true;
            } else {
                $contact->primaryContact = false;
            }
            $this->_em->persist($contact);
            $this->_em->flush();
        }

        // Return updated entity
        return $person;
    }
}