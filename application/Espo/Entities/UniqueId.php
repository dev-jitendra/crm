<?php


namespace Espo\Entities;

use Espo\Core\Field\DateTime;

use stdClass;
use LogicException;

class UniqueId extends \Espo\Core\ORM\Entity
{
    public const ENTITY_TYPE = 'UniqueId';

    public function getIdValue(): ?string
    {
        return $this->get('name');
    }

    public function getTerminateAt(): ?DateTime
    {
        
        return $this->getValueObject('terminateAt');
    }

    public function getData(): stdClass
    {
        return $this->get('data') ?? (object) [];
    }

    public function getCreatedAt(): DateTime
    {
        
        $value = $this->getValueObject('createdAt');

        if (!$value) {
            throw new LogicException();
        }

        return $value;
    }
}
