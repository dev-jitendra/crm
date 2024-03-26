<?php


namespace Espo\Entities;

use Espo\Core\ORM\Entity;
use UnexpectedValueException;

class Template extends Entity
{
    public const ENTITY_TYPE = 'Template';

    public function getTargetEntityType(): string
    {
        $entityType = $this->get('entityType');

        if ($entityType === null) {
            throw new UnexpectedValueException();
        }

        return $entityType;
    }
}
