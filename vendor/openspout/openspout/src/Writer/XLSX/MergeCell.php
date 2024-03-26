<?php

declare(strict_types=1);

namespace OpenSpout\Writer\XLSX;


final class MergeCell
{
    
    public function __construct(
        public readonly int $sheetIndex,
        public readonly int $topLeftColumn,
        public readonly int $topLeftRow,
        public readonly int $bottomRightColumn,
        public readonly int $bottomRightRow,
    ) {}
}
