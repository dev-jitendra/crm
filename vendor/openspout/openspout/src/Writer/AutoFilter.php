<?php

declare(strict_types=1);

namespace OpenSpout\Writer;


final class AutoFilter
{
    
    public function __construct(
        public readonly int $fromColumnIndex,
        public readonly int $fromRow,
        public readonly int $toColumnIndex,
        public readonly int $toRow
    ) {}
}
