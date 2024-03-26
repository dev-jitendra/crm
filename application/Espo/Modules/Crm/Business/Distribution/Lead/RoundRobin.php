<?php


namespace Espo\Modules\Crm\Business\Distribution\Lead;

use Espo\Modules\Crm\Entities\Lead;
use Espo\ORM\EntityManager;

use Espo\Entities\User;
use Espo\Entities\Team;

class RoundRobin
{
    
    protected $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    
    public function getUser($team, $targetUserPosition = null)
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

        $lead = $this->entityManager
            ->getRDBRepository(Lead::ENTITY_TYPE)
            ->where([
                'assignedUserId' => $userIdList
            ])
            ->order('createdAt', 'DESC')
            ->findOne();

        if (empty($lead)) {
            $num = 0;
        }
        else {
            $num = array_search($lead->get('assignedUserId'), $userIdList);

            if ($num === false || $num == count($userIdList) - 1) {
                $num = 0;
            } else {
                $num++;
            }
        }

        return $this->entityManager->getEntity(User::ENTITY_TYPE, $userIdList[$num]);
    }
}
