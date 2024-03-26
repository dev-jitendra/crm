<?php


namespace Espo\Modules\Crm\Hooks\Contact;

use Espo\Core\Hook\Hook\AfterSave;
use Espo\Modules\Crm\Entities\Contact;
use Espo\ORM\Entity;
use Espo\ORM\EntityManager;
use Espo\ORM\Repository\Option\SaveOptions;


class Accounts implements AfterSave
{
    public function __construct(private EntityManager $entityManager) {}

    
    public function afterSave(Entity $entity, SaveOptions $options): void
    {
        $accountIdChanged = $entity->isAttributeChanged('accountId');
        $titleChanged = $entity->isAttributeChanged('title');

        
        $fetchedAccountId = $entity->getFetched('accountId');
        $accountId = $entity->getAccount()?->getId();
        $title = $entity->getTitle();

        $relation = $this->entityManager
            ->getRDBRepositoryByClass(Contact::class)
            ->getRelation($entity, 'accounts');

        if (!$accountId && $fetchedAccountId) {
            $relation->unrelateById($fetchedAccountId);

            return;
        }

        if (!$accountIdChanged && !$titleChanged) {
            return;
        }

        if (!$accountId) {
            return;
        }

        $accountContact = $this->entityManager
            ->getRDBRepository('AccountContact')
            ->select(['role'])
            ->where([
                'accountId' => $accountId,
                'contactId' => $entity->getId(),
                'deleted' => false,
            ])
            ->findOne();

        if (!$accountContact && $accountIdChanged) {
            $relation->relateById($accountId, ['role' => $title]);

            return;
        }

        if ($titleChanged && $accountContact && $title !== $accountContact->get('role')) {
            $relation->updateColumnsById($accountId, ['role' => $title]);
        }
    }
}
