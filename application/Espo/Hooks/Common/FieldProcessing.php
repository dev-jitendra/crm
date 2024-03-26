<?php


namespace Espo\Hooks\Common;

use Espo\ORM\Entity;
use Espo\Core\FieldProcessing\SaveProcessor;
use Espo\Core\ORM\Entity as CoreEntity;

class FieldProcessing
{
    public static int $order = -11;

    private SaveProcessor $saveProcessor;

    public function __construct(SaveProcessor $saveProcessor)
    {
        $this->saveProcessor = $saveProcessor;
    }

    
    public function afterSave(Entity $entity, array $options): void
    {
        if (!empty($options['skipFieldProcessing'])) {
            return;
        }

        if (!$entity instanceof CoreEntity) {
            return;
        }

        $this->saveProcessor->process($entity, $options);
    }
}
