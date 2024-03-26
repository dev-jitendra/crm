<?php


namespace Espo\Classes\Acl\Email\LinkCheckers;

use Espo\Core\Acl\LinkChecker;
use Espo\Core\AclManager;
use Espo\Entities\Email;
use Espo\Entities\User;
use Espo\ORM\Entity;
use Espo\ORM\EntityManager;


class ParentLinkChecker implements LinkChecker
{
    public function __construct(
        private EntityManager $entityManager,
        private AclManager $aclManager
    ) {}

    public function check(User $user, Entity $entity, Entity $foreignEntity): bool
    {
        if ($this->aclManager->checkEntityRead($user, $foreignEntity)) {
            return true;
        }

        if (!$entity->getReplied()) {
            return false;
        }

        $replied = $this->entityManager
            ->getRepositoryByClass(Email::class)
            ->getById($entity->getReplied()->getId());

        if (!$replied) {
            return false;
        }

        $parentLink = $replied->getParent();

        if (
            !$parentLink ||
            $parentLink->getId() !== $foreignEntity->getId() ||
            $parentLink->getEntityType() !== $foreignEntity->getEntityType()
        ) {
            return false;
        }

        return $this->aclManager->checkEntityRead($user, $replied);
    }
}
