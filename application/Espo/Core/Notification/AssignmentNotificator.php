<?php


namespace Espo\Core\Notification;

use Espo\ORM\Entity;
use Espo\Core\Notification\AssignmentNotificator\Params;


interface AssignmentNotificator
{
    public function process(Entity $entity, Params $params): void;
}
