<?php


namespace Espo\Classes\Acl\EmailFilter;

use Espo\Entities\EmailAccount;
use Espo\Entities\User;
use Espo\Entities\EmailFilter;
use Espo\ORM\Entity;
use Espo\Core\Acl\OwnershipOwnChecker;
use Espo\Core\ORM\EntityManager;


class OwnershipChecker implements OwnershipOwnChecker
{
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    
    public function checkOwn(User $user, Entity $entity): bool
    {
        if ($entity->isGlobal()) {
            return false;
        }

        $parentType = $entity->getParentType();
        $parentId = $entity->getParentId();

        if (!$parentType || !$parentId) {
            return false;
        }

        $parent = $this->entityManager->getEntityById($parentType, $parentId);

        if (!$parent) {
            return false;
        }

        if ($parent->getEntityType() === User::ENTITY_TYPE) {
            return $parent->getId() === $user->getId();
        }

        if (
            $parent instanceof EmailAccount &&
            $parent->has('assignedUserId') &&
            $parent->get('assignedUserId') === $user->getId()
        ) {
            return true;
        }

        return false;
    }
}
