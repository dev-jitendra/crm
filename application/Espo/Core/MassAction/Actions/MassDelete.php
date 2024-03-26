<?php


namespace Espo\Core\MassAction\Actions;

use Espo\Core\Record\ActionHistory\Action;
use Espo\Entities\ActionHistoryRecord;
use Espo\Entities\User;
use Espo\Core\Acl;
use Espo\Core\Exceptions\Forbidden;
use Espo\Core\MassAction\Data;
use Espo\Core\MassAction\MassAction;
use Espo\Core\MassAction\Params;
use Espo\Core\MassAction\QueryBuilder;
use Espo\Core\MassAction\Result;
use Espo\Core\ORM\EntityManager;
use Espo\Core\Record\ServiceFactory;

class MassDelete implements MassAction
{
    public function __construct(
        private QueryBuilder $queryBuilder,
        private Acl $acl,
        private ServiceFactory $serviceFactory,
        private EntityManager $entityManager,
        private User $user
    ) {}

    public function process(Params $params, Data $data): Result
    {
        $entityType = $params->getEntityType();

        if (!$this->acl->check($entityType, Acl\Table::ACTION_DELETE)) {
            throw new Forbidden("No delete access for '{$entityType}'.");
        }

        if (
            !$params->hasIds() &&
            $this->acl->getPermissionLevel('massUpdate') !== Acl\Table::LEVEL_YES
        ) {
            throw new Forbidden("No mass-update permission.");
        }

        $service = $this->serviceFactory->create($entityType);

        $repository = $this->entityManager->getRDBRepository($entityType);

        $query = $this->queryBuilder->build($params);

        $collection = $repository
            ->clone($query)
            ->sth()
            ->find();

        $ids = [];

        $count = 0;

        foreach ($collection as $entity) {
            if (!$this->acl->checkEntityDelete($entity)) {
                continue;
            }

            $repository->remove($entity, [
                'modifiedById' => $this->user->getId(),
            ]);

            
            $id = $entity->getId();

            $ids[] = $id;

            $count++;

            $service->processActionHistoryRecord(Action::DELETE, $entity);
        }

        $result = [
            'count' => $count,
            'ids' => $ids,
        ];

        return Result::fromArray($result);
    }
}
