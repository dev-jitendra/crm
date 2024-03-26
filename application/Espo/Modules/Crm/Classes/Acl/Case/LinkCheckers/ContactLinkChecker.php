<?php


namespace Espo\Modules\Crm\Classes\Acl\Case\LinkCheckers;

use Espo\Core\Acl\LinkChecker;
use Espo\Core\AclManager;
use Espo\Entities\Email;
use Espo\Entities\User;
use Espo\Modules\Crm\Entities\CaseObj;
use Espo\Modules\Crm\Entities\Contact;
use Espo\ORM\Entity;
use Espo\ORM\EntityManager;


class ContactLinkChecker implements LinkChecker
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

        $emailIds = $entity->getLinkMultipleIdList('emails');

        if (count($emailIds) === 0 || count($emailIds) > 1) {
            return false;
        }

        $email = $this->entityManager
            ->getRepositoryByClass(Email::class)
            ->getById($emailIds[0]);

        if (!$email) {
            return false;
        }

        $parent = $email->getParent();

        if (!$parent) {
            return false;
        }

        if (
            $parent->getEntityType() !== Contact::ENTITY_TYPE ||
            $parent->getId() !== $foreignEntity->getId()
        ) {
            return false;
        }

        return $this->aclManager->checkEntityRead($user, $email);
    }
}
