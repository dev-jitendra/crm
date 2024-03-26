<?php


namespace Espo\Classes\Acl\WorkingTimeRange;

use Espo\Core\Acl\AssignmentChecker as AssignmentCheckerInterface;
use Espo\Core\Acl\DefaultAssignmentChecker;
use Espo\Core\AclManager;
use Espo\Entities\User;
use Espo\Entities\WorkingTimeRange;
use Espo\ORM\Entity;
use Espo\ORM\EntityManager;


class AssignmentChecker implements AssignmentCheckerInterface
{
    private DefaultAssignmentChecker $defaultAssignmentChecker;
    private AclManager $aclManager;
    private EntityManager $entityManager;

    public function __construct(
        DefaultAssignmentChecker $defaultAssignmentChecker,
        AclManager $aclManager,
        EntityManager $entityManager
    ) {
        $this->defaultAssignmentChecker = $defaultAssignmentChecker;
        $this->aclManager = $aclManager;
        $this->entityManager = $entityManager;
    }

    
    public function check(User $user, Entity $entity): bool
    {
        $result = $this->defaultAssignmentChecker->check($user, $entity);

        if (!$result) {
            return false;
        }

        if (!$entity->isAttributeChanged('usersIds')) {
            return true;
        }

        $users = $this->entityManager
            ->getRDBRepositoryByClass(User::class)
            ->where(['id' => $entity->getUsers()->getIdList()])
            ->find();

        foreach ($users as $targetUser) {
            $accessToUser = $this->aclManager->check($user, $targetUser);

            if (!$accessToUser) {
                return false;
            }
        }

        return true;
    }
}
