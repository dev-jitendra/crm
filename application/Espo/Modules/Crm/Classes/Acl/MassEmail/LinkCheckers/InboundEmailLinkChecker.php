<?php


namespace Espo\Modules\Crm\Classes\Acl\MassEmail\LinkCheckers;

use Espo\Core\Acl\LinkChecker;
use Espo\Entities\InboundEmail;
use Espo\Entities\User;
use Espo\Modules\Crm\Entities\MassEmail;
use Espo\ORM\Entity;


class InboundEmailLinkChecker implements LinkChecker
{
    public function check(User $user, Entity $entity, Entity $foreignEntity): bool
    {
        return $foreignEntity->smtpIsForMassEmail();
    }
}
