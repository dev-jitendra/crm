<?php


namespace Espo\Modules\Crm\Tools\Case\Distribution;

use Espo\Modules\Crm\Entities\CaseObj;
use Espo\ORM\EntityManager;
use Espo\Core\Utils\Metadata;

use Espo\Entities\User;
use Espo\Entities\Team;

class LeastBusy
{
    private EntityManager $entityManager;
    private Metadata $metadata;

    public function __construct(EntityManager $entityManager, Metadata $metadata)
    {
        $this->entityManager = $entityManager;
        $this->metadata = $metadata;
    }

    public function getUser(Team $team, ?string $targetUserPosition = null): ?User
    {
        $where = [
            'isActive' => true,
        ];

        if (!empty($targetUserPosition)) {
            $where['@relation.role'] = $targetUserPosition;
        }

        $userList = $this->entityManager
            ->getRDBRepository(Team::ENTITY_TYPE)
            ->getRelation($team, 'users')
            ->where($where)
            ->order('id')
            ->find();

        if (is_countable($userList) && count($userList) == 0) {
            return null;
        }

        $countHash = [];

        $notActualStatusList =
            $this->metadata->get(['entityDefs', 'Case', 'fields', 'status', 'notActualOptions']) ?? [];

        foreach ($userList as $user) {
            $count = $this->entityManager
                ->getRDBRepository(CaseObj::ENTITY_TYPE)
                ->where([
                    'assignedUserId' => $user->getId(),
                    'status!=' => $notActualStatusList,
                ])
                ->count();

            $countHash[$user->getId()] = $count;
        }

        $foundUserId = false;
        $min = false;

        foreach ($countHash as $userId => $count) {
            if ($min === false) {
                $min = $count;
                $foundUserId = $userId;
            }
            else if ($count < $min) {
                $min = $count;
                $foundUserId = $userId;
            }
        }

        if ($foundUserId !== false) {
            
            return $this->entityManager->getEntityById(User::ENTITY_TYPE, $foundUserId);
        }

        return null;
    }
}
