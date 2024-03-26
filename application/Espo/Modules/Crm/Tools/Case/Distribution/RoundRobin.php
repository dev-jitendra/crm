<?php


namespace Espo\Modules\Crm\Tools\Case\Distribution;

use Espo\Entities\User;
use Espo\Entities\Team;

use Espo\Modules\Crm\Entities\CaseObj;
use Espo\ORM\EntityManager;

class RoundRobin
{
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
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

        $userIdList = [];

        foreach ($userList as $user) {
            $userIdList[] = $user->getId();
        }

        
        $case = $this->entityManager
            ->getRDBRepository(CaseObj::ENTITY_TYPE)
            ->where([
                'assignedUserId' => $userIdList,
            ])
            ->order('number', 'DESC')
            ->findOne();

        if (empty($case)) {
            $num = 0;
        }
        else {
            $num = array_search($case->getAssignedUser()?->getId(), $userIdList);

            if ($num === false || $num == count($userIdList) - 1) {
                $num = 0;
            }
            else {
                $num++;
            }
        }

        $id = $userIdList[$num];

        
        return $this->entityManager->getEntityById(User::ENTITY_TYPE, $id);
    }
}
