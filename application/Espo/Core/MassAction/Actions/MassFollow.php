<?php


namespace Espo\Core\MassAction\Actions;

use Espo\Tools\Stream\Service as StreamService;
use Espo\Core\Acl;
use Espo\Core\Exceptions\Forbidden;
use Espo\Core\MassAction\Data;
use Espo\Core\MassAction\MassAction;
use Espo\Core\MassAction\Params;
use Espo\Core\MassAction\QueryBuilder;
use Espo\Core\MassAction\Result;
use Espo\Core\ORM\EntityManager;
use Espo\Entities\User;

class MassFollow implements MassAction
{
    private QueryBuilder $queryBuilder;
    private Acl $acl;
    private StreamService $streamService;
    private EntityManager $entityManager;
    private User $user;

    public function __construct(
        QueryBuilder $queryBuilder,
        Acl $acl,
        StreamService $streamService,
        EntityManager $entityManager,
        User $user
    ) {
        $this->queryBuilder = $queryBuilder;
        $this->acl = $acl;
        $this->streamService = $streamService;
        $this->entityManager = $entityManager;
        $this->user = $user;
    }

    public function process(Params $params, Data $data): Result
    {
        $entityType = $params->getEntityType();

        $passedUserId = $data->get('userId');

        if ($passedUserId && !$this->user->isAdmin()) {
            throw new Forbidden();
        }

        $userId = $passedUserId ?? $this->user->getId();

        if (!$this->acl->check($entityType, Acl\Table::ACTION_STREAM)) {
            throw new Forbidden("No stream access for '{$entityType}'.");
        }

        $query = $this->queryBuilder->build($params);

        $collection = $this->entityManager
            ->getRDBRepository($entityType)
            ->clone($query)
            ->sth()
            ->find();

        $ids = [];

        $count = 0;

        foreach ($collection as $entity) {
            if (
                !$this->acl->checkEntityStream($entity) ||
                !$this->acl->checkEntityRead($entity)
            ) {
                continue;
            }

            $followResult = $this->streamService->followEntity($entity, $userId);

            if (!$followResult) {
                continue;
            }

            
            $id = $entity->getId();

            $ids[] = $id;
            $count++;
        }

        return new Result($count, $ids);
    }
}
