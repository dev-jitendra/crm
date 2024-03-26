<?php


namespace Espo\Repositories;

use Espo\Core\Repositories\Database;
use Espo\Core\Utils\DateTime;
use Espo\Entities\Job as JobEntity;
use Espo\ORM\Entity;

use Espo\Core\Di;


class Job extends Database implements
    Di\ConfigAware
{
    use Di\ConfigSetter;

    protected $hooksDisabled = true;

    
    public function beforeSave(Entity $entity, array $options = [])
    {
        if ($entity->get('executeTime') === null && $entity->isNew()) {
            $entity->set('executeTime', DateTime::getSystemNowString());
        }

        if ($entity->get('attempts') === null && $entity->isNew()) {
            $attempts = $this->config->get('jobRerunAttemptNumber', 0);

            $entity->set('attempts', $attempts);
        }
    }
}
