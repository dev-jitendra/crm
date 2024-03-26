<?php


namespace Espo\Modules\Crm\Classes\Acl\Task\LinkCheckers;

use Espo\Core\Acl\LinkChecker;
use Espo\Core\AclManager;
use Espo\Entities\Email;
use Espo\Entities\User;
use Espo\Modules\Crm\Entities\Task;
use Espo\ORM\Entity;
use Espo\ORM\EntityManager;


class ParentLinkChecker implements LinkChecker
{
    public function __construct(
        private AclManager $aclManager,
        private EntityManager $entityManager
    ) {}

    public function check(User $user, Entity $entity, Entity $foreignEntity): bool
    {
        if ($this->aclManager->checkEntityRead($user, $foreignEntity)) {
            return true;
        }

        if (!$entity->isNew()) {
            return false;
        }

        
        $emailId = $entity->get('emailId');

        if (!$emailId) {
            return false;
        }

        $email = $this->entityManager
            ->getRepositoryByClass(Email::class)
            ->getById($emailId);

        if (!$email) {
            return false;
        }

        $parent = $email->getParent();

        if (!$parent) {
            return false;
        }

        if (
            $parent->getEntityType() !== $foreignEntity->getEntityType() ||
            $parent->getId() !== $foreignEntity->getId()
        ) {
            return false;
        }

        return $this->aclManager->checkEntityRead($user, $email);
    }
}
