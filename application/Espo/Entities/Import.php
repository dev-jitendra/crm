<?php


namespace Espo\Entities;

use Espo\Core\ORM\Entity;
use Espo\Tools\Import\Params;

class Import extends Entity
{
    public const ENTITY_TYPE = 'Import';
    public const STATUS_STANDBY = 'Standby';
    public const STATUS_IN_PROCESS = 'In Process';
    public const STATUS_FAILED = 'Failed';
    public const STATUS_PENDING = 'Pending';
    public const STATUS_COMPLETE = 'Complete';

    public function getStatus(): ?string
    {
        return $this->get('status');
    }

    public function getParams(): Params
    {
        $raw = $this->get('params');

        return Params::fromRaw($raw);
    }

    public function getFileId(): ?string
    {
        return $this->get('fileId');
    }

    public function getTargetEntityType(): ?string
    {
        return $this->get('entityType');
    }

    
    public function getTargetAttributeList(): ?array
    {
        return $this->get('attributeList');
    }
}
