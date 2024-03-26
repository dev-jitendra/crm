<?php


namespace Espo\Core\Utils\Database;

use Doctrine\DBAL\Types\Types;
use Espo\Core\Utils\Metadata;

class MetadataProvider
{
    private const DEFAULT_ID_LENGTH = 24;
    private const DEFAULT_ID_DB_TYPE = Types::STRING;

    public function __construct(private Metadata $metadata)
    {}

    public function getIdLength(): int
    {
        return $this->metadata->get(['app', 'recordId', 'length']) ??
            self::DEFAULT_ID_LENGTH;
    }

    public function getIdDbType(): string
    {
        return $this->metadata->get(['app', 'recordId', 'dbType']) ??
            self::DEFAULT_ID_DB_TYPE;
    }
}
