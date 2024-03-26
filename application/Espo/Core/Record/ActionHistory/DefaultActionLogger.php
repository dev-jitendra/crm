<?php


namespace Espo\Core\Record\ActionHistory;

use Espo\Core\Field\LinkParent;
use Espo\Entities\ActionHistoryRecord;
use Espo\Entities\User;
use Espo\ORM\Entity;
use Espo\ORM\EntityManager;

class DefaultActionLogger implements ActionLogger
{
    public function __construct(
        private EntityManager $entityManager,
        private User $user
    ) {}

    
    public function log(string $action, Entity $entity): void
    {
        $historyRecord = $this->entityManager
            ->getRepositoryByClass(ActionHistoryRecord::class)
            ->getNew();

        $historyRecord
            ->setAction($action)
            ->setUserId($this->user->getId())
            ->setAuthTokenId($this->user->get('authTokenId'))
            ->setAuthLogRecordId($this->user->get('authLogRecordId'))
            ->setIpAddress($this->user->get('ipAddress'))
            ->setTarget(LinkParent::createFromEntity($entity));

        $this->entityManager->saveEntity($historyRecord);
    }
}
