<?php


namespace Espo\Modules\Crm\Services;

use Espo\Entities\Email;
use Espo\Modules\Crm\Entities\CaseObj as CaseEntity;
use Espo\Modules\Crm\Entities\Contact as ContactEntity;
use Espo\ORM\Entity;
use Espo\Services\Record;


class CaseObj extends Record
{
    
    public function beforeCreateEntity(Entity $entity, $data)
    {
        parent::beforeCreateEntity($entity, $data);

        if ($this->user->isPortal()) {
            if (!$entity->has('accountId')) {
                if ($this->user->getContactId()) {
                    
                    $contact = $this->entityManager
                        ->getEntityById(ContactEntity::ENTITY_TYPE, $this->user->getContactId());

                    if ($contact && $contact->getAccount()) {
                        $entity->set('accountId', $contact->getAccount()->getId());
                    }
                }
            }

            if (!$entity->has('contactId')) {
                if ($this->user->getContactId()) {
                    $entity->set('contactId', $this->user->getContactId());
                }
            }
        }
    }

    public function afterCreateEntity(Entity $entity, $data)
    {
        parent::afterCreateEntity($entity, $data);

        if (!empty($data->emailId)) {
            
            $email = $this->entityManager->getEntityById(Email::ENTITY_TYPE, $data->emailId);

            if ($email && !$email->getParentId() && $this->acl->check($email)) {
                $email->set([
                    'parentType' => CaseEntity::ENTITY_TYPE,
                    'parentId' => $entity->getId(),
                ]);

                $this->entityManager->saveEntity($email);
            }
        }
    }
}
