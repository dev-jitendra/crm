<?php

declare(strict_types=1);

namespace OpenSpout\Reader\Common;


final class ColumnWidth
{
    
    public function __construct(
        public readonly int $start,
        public readonly int $end,
        public readonly float $width,
    ) {}
}
