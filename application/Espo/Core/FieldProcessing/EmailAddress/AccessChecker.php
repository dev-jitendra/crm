<?php


namespace Espo\Core\FieldProcessing\EmailAddress;

use Espo\Repositories\EmailAddress as Repository;
use Espo\ORM\Entity;

use Espo\Entities\EmailAddress;
use Espo\Entities\User;

use Espo\Core\AclManager;
use Espo\Core\ORM\EntityManager;

class AccessChecker
{
    public function __construct(
        private EntityManager $entityManager,
        private AclManager $aclManager
    ) {}

    public function checkEdit(User $user, EmailAddress $emailAddress, Entity $excludeEntity): bool
    {
        
        $repository = $this->entityManager->getRepository('EmailAddress');

        $entityWithSameAddressList = $repository->getEntityListByAddressId($emailAddress->getId(), $excludeEntity);

        foreach ($entityWithSameAddressList as $e) {
            if ($this->aclManager->checkEntityEdit($user, $e)) {
                continue;
            }

            if (
                $e instanceof User &&
                $e->isPortal() &&
                $excludeEntity->getEntityType() === 'Contact' &&
                $e->get('contactId') === $excludeEntity->getEntityType()
            ) {
                continue;
            }

            return false;
        }

        return true;
    }
}
