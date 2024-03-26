<?php


namespace Espo\Hooks\Common;

use Espo\ORM\Entity;

use Espo\Core\FieldProcessing\NextNumber\BeforeSaveProcessor as Processor;

class NextNumber
{
    private Processor $processor;

    public function __construct(Processor $processor)
    {
        $this->processor = $processor;
    }

    
    public function beforeSave(Entity $entity, array $options): void
    {
        $this->processor->process($entity, $options);
    }
}
