<?php


namespace Espo\Classes\RecordHooks\Team;

use Espo\Core\Exceptions\Forbidden;
use Espo\Core\Record\Hook\LinkHook;

use Espo\ORM\Entity;

use Espo\Entities\User;


class BeforeLinkUserCheck implements LinkHook
{
    public function process(Entity $entity, string $link, Entity $foreignEntity): void
    {
        if ($link !== 'users') {
            return;
        }

        assert($foreignEntity instanceof User);

        $this->processUserCheck($foreignEntity);
    }

    private function processUserCheck(User $user): void
    {
        if ($user->isPortal()) {
            throw new Forbidden("Can't add portal users to team.");
        }

        if ($user->isSystem()) {
            throw new Forbidden("Can't add system users to team.");
        }
    }
}
