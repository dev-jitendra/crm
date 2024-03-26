<?php


namespace Espo\Core\MassAction\Actions;

use Espo\Tools\Stream\Service as StreamService;
use Espo\Core\Exceptions\Forbidden;
use Espo\Core\MassAction\Data;
use Espo\Core\MassAction\MassAction;
use Espo\Core\MassAction\Params;
use Espo\Core\MassAction\QueryBuilder;
use Espo\Core\MassAction\Result;
use Espo\Core\ORM\EntityManager;
use Espo\Entities\User;

class MassUnfollow implements MassAction
{
    public function __construct(
        private QueryBuilder $queryBuilder,
        private StreamService $streamService,
        private EntityManager $entityManager,
        private User $user
    ) {}

    public function process(Params $params, Data $data): Result
    {
        $entityType = $params->getEntityType();

        $passedUserId = $data->get('userId');

        if ($passedUserId && !$this->user->isAdmin()) {
            throw new Forbidden();
        }

        $userId = $passedUserId ?? $this->user->getId();

        $query = $this->queryBuilder->build($params);

        $collection = $this->entityManager
            ->getRDBRepository($entityType)
            ->clone($query)
            ->sth()
            ->find();

        $ids = [];

        $count = 0;

        foreach ($collection as $entity) {
            $this->streamService->unfollowEntity($entity, $userId);

            
            $id = $entity->getId();

            $ids[] = $id;
            $count++;
        }

        return new Result($count, $ids);
    }
}
