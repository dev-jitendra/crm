<?php


namespace Espo\Modules\Crm\Repositories;
use Espo\Modules\Crm\Entities\Task as TaskEntity;
use Espo\Core\Repositories\Event as EventRepository;

class Task extends EventRepository
{
    protected $reminderSkippingStatusList = [
        TaskEntity::STATUS_COMPLETED,
        TaskEntity::STATUS_CANCELED,
    ];
}
