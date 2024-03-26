<?php


namespace Espo\Modules\Crm\Classes\AclPortal\Contact;

use Espo\Entities\User;
use Espo\Modules\Crm\Entities\Contact;
use Espo\ORM\Entity;
use Espo\Core\Portal\Acl\DefaultOwnershipChecker;
use Espo\Core\Portal\Acl\OwnershipAccountChecker;
use Espo\Core\Portal\Acl\OwnershipContactChecker;


class OwnershipChecker implements OwnershipAccountChecker, OwnershipContactChecker
{
    public function __construct(private DefaultOwnershipChecker $defaultOwnershipChecker) {}

    public function checkContact(User $user, Entity $entity): bool
    {
        $contactId = $user->get('contactId');

        if ($contactId) {
            if ($entity->getId() === $contactId) {
                return true;
            }
        }

        return false;
    }

    public function checkAccount(User $user, Entity $entity): bool
    {
        return $this->defaultOwnershipChecker->checkAccount($user, $entity);
    }
}
