<?php


namespace Espo\Hooks\Common;

use Espo\ORM\Entity;

use Espo\Core\FieldProcessing\VersionNumber\BeforeSaveProcessor as Processor;

class VersionNumber
{
    private Processor $processor;

    public function __construct(Processor $processor)
    {
        $this->processor = $processor;
    }

    public function beforeSave(Entity $entity): void
    {
        $this->processor->process($entity);
    }
}
