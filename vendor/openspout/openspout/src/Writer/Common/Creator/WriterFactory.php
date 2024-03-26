<?php

declare(strict_types=1);

namespace OpenSpout\Writer\Common\Creator;

use OpenSpout\Common\Exception\UnsupportedTypeException;
use OpenSpout\Writer\CSV\Writer as CSVWriter;
use OpenSpout\Writer\ODS\Writer as ODSWriter;
use OpenSpout\Writer\WriterInterface;
use OpenSpout\Writer\XLSX\Writer as XLSXWriter;


final class WriterFactory
{
    
    public static function createFromFile(string $path): WriterInterface
    {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        return match ($extension) {
            'csv' => new CSVWriter(),
            'xlsx' => new XLSXWriter(),
            'ods' => new ODSWriter(),
            default => throw new UnsupportedTypeException('No writers supporting the given type: '.$extension),
        };
    }
}
