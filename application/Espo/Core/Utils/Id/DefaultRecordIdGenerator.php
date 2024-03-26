<?php


namespace Espo\Core\Utils\Id;

use Espo\Core\Utils\Metadata;
use Espo\Core\Utils\Util;


class DefaultRecordIdGenerator implements RecordIdGenerator
{
    private bool $isUuid;

    public function __construct(Metadata $metadata)
    {
        $this->isUuid =
            $metadata->get(['app', 'recordId', 'type']) === 'uuid4' ||
            $metadata->get(['app', 'recordId', 'dbType']) === 'uuid';
    }

    public function generate(): string
    {
        return $this->isUuid ?
            Util::generateUuid4() :
            Util::generateId();
    }
}
