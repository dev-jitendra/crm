<?php


namespace Espo\Core\Notification;

use Espo\Core\Mail\SenderParams;
use Espo\ORM\Entity;
use Espo\Entities\User;
use Espo\Entities\Email;


interface EmailNotificationHandler
{
    public function prepareEmail(Email $email, Entity $entity, User $user): void;

    public function getSenderParams(Entity $entity, User $user): ?SenderParams;
}
