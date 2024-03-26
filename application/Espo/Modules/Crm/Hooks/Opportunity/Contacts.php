<?php


namespace Espo\Modules\Crm\Hooks\Opportunity;

use Espo\Core\Hook\Hook\AfterSave;
use Espo\Modules\Crm\Entities\Contact;
use Espo\Modules\Crm\Entities\Opportunity;
use Espo\ORM\Entity;
use Espo\ORM\EntityManager;
use Espo\ORM\Repository\Option\SaveOptions;


class Contacts implements AfterSave
{
    public function __construct(private EntityManager $entityManager) {}

    
    public function afterSave(Entity $entity, SaveOptions $options): void
    {
        if (!$entity->isAttributeChanged('contactId')) {
            return;
        }

        
        $contactId = $entity->get('contactId');
        $contactIdList = $entity->get('contactsIds') ?? [];
        $fetchedContactId = $entity->getFetched('contactId');

        $relation = $this->entityManager
            ->getRDBRepositoryByClass(Opportunity::class)
            ->getRelation($entity, 'contacts');

        if (!$contactId) {
            if ($fetchedContactId) {
                $relation->unrelateById($fetchedContactId);
            }

            return;
        }

        if (in_array($contactId, $contactIdList)) {
            return;
        }

        $contact = $this->entityManager
            ->getRDBRepositoryByClass(Contact::class)
            ->getById($contactId);

        if (!$contact) {
            return;
        }

        if ($relation->isRelated($contact)) {
            return;
        }

        $relation->relateById($contactId);
    }
}
