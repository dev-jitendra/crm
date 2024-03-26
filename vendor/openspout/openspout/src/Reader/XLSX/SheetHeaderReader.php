<?php

declare(strict_types=1);

namespace OpenSpout\Reader\XLSX;

use OpenSpout\Common\Exception\IOException;
use OpenSpout\Reader\Common\ColumnWidth;
use OpenSpout\Reader\Common\XMLProcessor;
use OpenSpout\Reader\Wrapper\XMLReader;

final class SheetHeaderReader
{
    public const XML_NODE_COL = 'col';
    public const XML_NODE_SHEETDATA = 'sheetData';
    public const XML_ATTRIBUTE_MIN = 'min';
    public const XML_ATTRIBUTE_MAX = 'max';
    public const XML_ATTRIBUTE_WIDTH = 'width';

    
    private readonly string $filePath;

    
    private readonly string $sheetDataXMLFilePath;

    
    private readonly XMLReader $xmlReader;

    
    private readonly XMLProcessor $xmlProcessor;

    
    private array $columnWidths = [];

    
    public function __construct(
        string $filePath,
        string $sheetDataXMLFilePath,
        XMLReader $xmlReader,
        XMLProcessor $xmlProcessor
    ) {
        $this->filePath = $filePath;
        $this->sheetDataXMLFilePath = $this->normalizeSheetDataXMLFilePath($sheetDataXMLFilePath);
        $this->xmlReader = $xmlReader;

        
        $this->xmlProcessor = $xmlProcessor;
        $this->xmlProcessor->registerCallback(self::XML_NODE_COL, XMLProcessor::NODE_TYPE_START, [$this, 'processColStartingNode']);
        $this->xmlProcessor->registerCallback(self::XML_NODE_SHEETDATA, XMLProcessor::NODE_TYPE_START, [$this, 'processSheetDataStartingNode']);

        
        $this->xmlReader->close();

        if (false === $this->xmlReader->openFileInZip($this->filePath, $this->sheetDataXMLFilePath)) {
            throw new IOException("Could not open \"{$this->sheetDataXMLFilePath}\".");
        }

        
        $this->xmlProcessor->readUntilStopped();

        
        $this->xmlReader->close();
    }

    
    public function getColumnWidths(): array
    {
        return $this->columnWidths;
    }

    
    private function processColStartingNode(XMLReader $xmlReader): int
    {
        $min = (int) $xmlReader->getAttribute(self::XML_ATTRIBUTE_MIN);
        $max = (int) $xmlReader->getAttribute(self::XML_ATTRIBUTE_MAX);
        $width = (float) $xmlReader->getAttribute(self::XML_ATTRIBUTE_WIDTH);

        \assert($min > 0);
        \assert($max > 0);

        $columnwidth = new ColumnWidth($min, $max, $width);
        $this->columnWidths[] = $columnwidth;

        return XMLProcessor::PROCESSING_CONTINUE;
    }

    
    private function processSheetDataStartingNode(): int
    {
        
        return XMLProcessor::PROCESSING_STOP;
    }

    
    private function normalizeSheetDataXMLFilePath(string $sheetDataXMLFilePath): string
    {
        return ltrim($sheetDataXMLFilePath, '/');
    }
}
