<?php


namespace Espo\Services;

use Espo\ORM\Entity;

use Espo\Core\Exceptions\BadRequest;

use Cron\CronExpression;

use Exception;


class ScheduledJob extends Record
{
    
    protected bool $findLinkedLogCountQueryDisabled = true;

    public function processValidation(Entity $entity, $data)
    {
        parent::processValidation($entity, $data);

        $scheduling = $entity->get('scheduling');

        try {
            $cronExpression = CronExpression::factory($scheduling);

            
            $cronExpression->getNextRunDate()->format('Y-m-d H:i:s');
        }
        catch (Exception $e) {
            throw new BadRequest("Not valid scheduling expression.");
        }
    }
}
