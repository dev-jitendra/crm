<?php


namespace Espo\Core\FieldProcessing\PhoneNumber;

use Espo\Repositories\PhoneNumber as Repository;
use Espo\ORM\Entity;
use Espo\Entities\PhoneNumber;
use Espo\Entities\User;
use Espo\Core\AclManager;
use Espo\Core\ORM\EntityManager;

class AccessChecker
{
    public function __construct(
        private EntityManager $entityManager,
        private AclManager $aclManager
    ) {}

    public function checkEdit(User $user, PhoneNumber $phoneNumber, Entity $excludeEntity): bool
    {
        
        $repository = $this->entityManager->getRepository('PhoneNumber');

        $entityWithSameNumberList = $repository->getEntityListByPhoneNumberId($phoneNumber->getId(), $excludeEntity);

        foreach ($entityWithSameNumberList as $e) {
            if ($this->aclManager->checkEntityEdit($user, $e)) {
                continue;
            }

            if (
                $e instanceof User &&
                $e->isPortal() &&
                $excludeEntity->getEntityType() === 'Contact' &&
                $e->get('contactId') === $excludeEntity->getId()
            ) {
                continue;
            }

            return false;
        }

        return true;
    }
}
