<?php

declare(strict_types=1);

namespace OpenSpout\Reader\XLSX\Manager;

use OpenSpout\Common\Helper\Escaper\XLSX;
use OpenSpout\Reader\Common\Manager\RowManager;
use OpenSpout\Reader\Common\XMLProcessor;
use OpenSpout\Reader\Wrapper\XMLReader;
use OpenSpout\Reader\XLSX\Helper\CellValueFormatter;
use OpenSpout\Reader\XLSX\Options;
use OpenSpout\Reader\XLSX\RowIterator;
use OpenSpout\Reader\XLSX\Sheet;
use OpenSpout\Reader\XLSX\SheetHeaderReader;


final class SheetManager
{
    
    public const WORKBOOK_XML_RELS_FILE_PATH = 'xl/_rels/workbook.xml.rels';
    public const WORKBOOK_XML_FILE_PATH = 'xl/workbook.xml';

    
    public const XML_NODE_WORKBOOK_PROPERTIES = 'workbookPr';
    public const XML_NODE_WORKBOOK_VIEW = 'workbookView';
    public const XML_NODE_SHEET = 'sheet';
    public const XML_NODE_SHEETS = 'sheets';
    public const XML_NODE_RELATIONSHIP = 'Relationship';

    
    public const XML_ATTRIBUTE_DATE_1904 = 'date1904';
    public const XML_ATTRIBUTE_ACTIVE_TAB = 'activeTab';
    public const XML_ATTRIBUTE_R_ID = 'r:id';
    public const XML_ATTRIBUTE_NAME = 'name';
    public const XML_ATTRIBUTE_STATE = 'state';
    public const XML_ATTRIBUTE_ID = 'Id';
    public const XML_ATTRIBUTE_TARGET = 'Target';

    
    public const SHEET_STATE_HIDDEN = 'hidden';

    
    private readonly string $filePath;

    private readonly Options $options;

    
    private readonly SharedStringsManager $sharedStringsManager;

    
    private readonly XLSX $escaper;

    
    private array $sheets;

    
    private int $currentSheetIndex;

    
    private int $activeSheetIndex;

    public function __construct(
        string $filePath,
        Options $options,
        SharedStringsManager $sharedStringsManager,
        XLSX $escaper
    ) {
        $this->filePath = $filePath;
        $this->options = $options;
        $this->sharedStringsManager = $sharedStringsManager;
        $this->escaper = $escaper;
    }

    
    public function getSheets(): array
    {
        $this->sheets = [];
        $this->currentSheetIndex = 0;
        $this->activeSheetIndex = 0; 

        $xmlReader = new XMLReader();
        $xmlProcessor = new XMLProcessor($xmlReader);

        $xmlProcessor->registerCallback(self::XML_NODE_WORKBOOK_PROPERTIES, XMLProcessor::NODE_TYPE_START, [$this, 'processWorkbookPropertiesStartingNode']);
        $xmlProcessor->registerCallback(self::XML_NODE_WORKBOOK_VIEW, XMLProcessor::NODE_TYPE_START, [$this, 'processWorkbookViewStartingNode']);
        $xmlProcessor->registerCallback(self::XML_NODE_SHEET, XMLProcessor::NODE_TYPE_START, [$this, 'processSheetStartingNode']);
        $xmlProcessor->registerCallback(self::XML_NODE_SHEETS, XMLProcessor::NODE_TYPE_END, [$this, 'processSheetsEndingNode']);

        if ($xmlReader->openFileInZip($this->filePath, self::WORKBOOK_XML_FILE_PATH)) {
            $xmlProcessor->readUntilStopped();
            $xmlReader->close();
        }

        return $this->sheets;
    }

    
    private function processWorkbookPropertiesStartingNode(XMLReader $xmlReader): int
    {
        
        
        $shouldUse1904Dates = filter_var($xmlReader->getAttribute(self::XML_ATTRIBUTE_DATE_1904), FILTER_VALIDATE_BOOLEAN);
        $this->options->SHOULD_USE_1904_DATES = $shouldUse1904Dates;

        return XMLProcessor::PROCESSING_CONTINUE;
    }

    
    private function processWorkbookViewStartingNode(XMLReader $xmlReader): int
    {
        
        
        $this->activeSheetIndex = (int) $xmlReader->getAttribute(self::XML_ATTRIBUTE_ACTIVE_TAB);

        return XMLProcessor::PROCESSING_CONTINUE;
    }

    
    private function processSheetStartingNode(XMLReader $xmlReader): int
    {
        $isSheetActive = ($this->currentSheetIndex === $this->activeSheetIndex);
        $this->sheets[] = $this->getSheetFromSheetXMLNode($xmlReader, $this->currentSheetIndex, $isSheetActive);
        ++$this->currentSheetIndex;

        return XMLProcessor::PROCESSING_CONTINUE;
    }

    
    private function processSheetsEndingNode(): int
    {
        return XMLProcessor::PROCESSING_STOP;
    }

    
    private function getSheetFromSheetXMLNode(XMLReader $xmlReaderOnSheetNode, int $sheetIndexZeroBased, bool $isSheetActive): Sheet
    {
        $sheetId = $xmlReaderOnSheetNode->getAttribute(self::XML_ATTRIBUTE_R_ID);
        \assert(null !== $sheetId);

        $sheetState = $xmlReaderOnSheetNode->getAttribute(self::XML_ATTRIBUTE_STATE);
        $isSheetVisible = (self::SHEET_STATE_HIDDEN !== $sheetState);

        $escapedSheetName = $xmlReaderOnSheetNode->getAttribute(self::XML_ATTRIBUTE_NAME);
        \assert(null !== $escapedSheetName);
        $sheetName = $this->escaper->unescape($escapedSheetName);

        $sheetDataXMLFilePath = $this->getSheetDataXMLFilePathForSheetId($sheetId);

        return new Sheet(
            $this->createRowIterator($this->filePath, $sheetDataXMLFilePath, $this->options, $this->sharedStringsManager),
            $this->createSheetHeaderReader($this->filePath, $sheetDataXMLFilePath),
            $sheetIndexZeroBased,
            $sheetName,
            $isSheetActive,
            $isSheetVisible
        );
    }

    
    private function getSheetDataXMLFilePathForSheetId(string $sheetId): string
    {
        $sheetDataXMLFilePath = '';

        
        $xmlReader = new XMLReader();
        if ($xmlReader->openFileInZip($this->filePath, self::WORKBOOK_XML_RELS_FILE_PATH)) {
            while ($xmlReader->read()) {
                if ($xmlReader->isPositionedOnStartingNode(self::XML_NODE_RELATIONSHIP)) {
                    $relationshipSheetId = $xmlReader->getAttribute(self::XML_ATTRIBUTE_ID);

                    if ($relationshipSheetId === $sheetId) {
                        
                        
                        $sheetDataXMLFilePath = $xmlReader->getAttribute(self::XML_ATTRIBUTE_TARGET);
                        \assert(null !== $sheetDataXMLFilePath);

                        
                        if (!str_starts_with($sheetDataXMLFilePath, '/xl/')) {
                            $sheetDataXMLFilePath = '/xl/'.$sheetDataXMLFilePath;

                            break;
                        }
                    }
                }
            }

            $xmlReader->close();
        }

        return $sheetDataXMLFilePath;
    }

    private function createRowIterator(
        string $filePath,
        string $sheetDataXMLFilePath,
        Options $options,
        SharedStringsManager $sharedStringsManager
    ): RowIterator {
        $xmlReader = new XMLReader();

        $workbookRelationshipsManager = new WorkbookRelationshipsManager($filePath);
        $styleManager = new StyleManager(
            $filePath,
            $workbookRelationshipsManager->hasStylesXMLFile()
                ? $workbookRelationshipsManager->getStylesXMLFilePath()
                : null
        );

        $cellValueFormatter = new CellValueFormatter(
            $sharedStringsManager,
            $styleManager,
            $options->SHOULD_FORMAT_DATES,
            $options->SHOULD_USE_1904_DATES,
            new XLSX()
        );

        return new RowIterator(
            $filePath,
            $sheetDataXMLFilePath,
            $options->SHOULD_PRESERVE_EMPTY_ROWS,
            $xmlReader,
            new XMLProcessor($xmlReader),
            $cellValueFormatter,
            new RowManager()
        );
    }

    private function createSheetHeaderReader(
        string $filePath,
        string $sheetDataXMLFilePath
    ): SheetHeaderReader {
        $xmlReader = new XMLReader();

        return new SheetHeaderReader(
            $filePath,
            $sheetDataXMLFilePath,
            $xmlReader,
            new XMLProcessor($xmlReader)
        );
    }
}
