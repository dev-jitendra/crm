<?php

declare(strict_types=1);

namespace Doctrine\DBAL\Platforms\MySQL;


interface CollationMetadataProvider
{
    public function getCollationCharset(string $collation): ?string;
}
