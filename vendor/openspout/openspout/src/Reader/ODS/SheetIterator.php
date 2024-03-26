<?php

declare(strict_types=1);

namespace OpenSpout\Reader\ODS;

use DOMElement;
use OpenSpout\Common\Exception\IOException;
use OpenSpout\Common\Helper\Escaper\ODS;
use OpenSpout\Reader\Common\XMLProcessor;
use OpenSpout\Reader\Exception\XMLProcessingException;
use OpenSpout\Reader\ODS\Helper\CellValueFormatter;
use OpenSpout\Reader\ODS\Helper\SettingsHelper;
use OpenSpout\Reader\SheetIteratorInterface;
use OpenSpout\Reader\Wrapper\XMLReader;


final class SheetIterator implements SheetIteratorInterface
{
    public const CONTENT_XML_FILE_PATH = 'content.xml';

    public const XML_STYLE_NAMESPACE = 'urn:oasis:names:tc:opendocument:xmlns:style:1.0';

    
    public const XML_NODE_AUTOMATIC_STYLES = 'office:automatic-styles';
    public const XML_NODE_STYLE_TABLE_PROPERTIES = 'table-properties';
    public const XML_NODE_TABLE = 'table:table';
    public const XML_ATTRIBUTE_STYLE_NAME = 'style:name';
    public const XML_ATTRIBUTE_TABLE_NAME = 'table:name';
    public const XML_ATTRIBUTE_TABLE_STYLE_NAME = 'table:style-name';
    public const XML_ATTRIBUTE_TABLE_DISPLAY = 'table:display';

    
    private readonly string $filePath;

    private readonly Options $options;

    
    private readonly XMLReader $xmlReader;

    
    private readonly ODS $escaper;

    
    private bool $hasFoundSheet;

    
    private int $currentSheetIndex;

    
    private readonly ?string $activeSheetName;

    
    private array $sheetsVisibility;

    public function __construct(
        string $filePath,
        Options $options,
        ODS $escaper,
        SettingsHelper $settingsHelper
    ) {
        $this->filePath = $filePath;
        $this->options = $options;
        $this->xmlReader = new XMLReader();
        $this->escaper = $escaper;
        $this->activeSheetName = $settingsHelper->getActiveSheetName($filePath);
    }

    
    public function rewind(): void
    {
        $this->xmlReader->close();

        if (false === $this->xmlReader->openFileInZip($this->filePath, self::CONTENT_XML_FILE_PATH)) {
            $contentXmlFilePath = $this->filePath.'#'.self::CONTENT_XML_FILE_PATH;

            throw new IOException("Could not open \"{$contentXmlFilePath}\".");
        }

        try {
            $this->sheetsVisibility = $this->readSheetsVisibility();
            $this->hasFoundSheet = $this->xmlReader->readUntilNodeFound(self::XML_NODE_TABLE);
        } catch (XMLProcessingException $exception) {
            throw new IOException("The content.xml file is invalid and cannot be read. [{$exception->getMessage()}]");
        }

        $this->currentSheetIndex = 0;
    }

    
    public function valid(): bool
    {
        $valid = $this->hasFoundSheet;
        if (!$valid) {
            $this->xmlReader->close();
        }

        return $valid;
    }

    
    public function next(): void
    {
        $this->hasFoundSheet = $this->xmlReader->readUntilNodeFound(self::XML_NODE_TABLE);

        if ($this->hasFoundSheet) {
            ++$this->currentSheetIndex;
        }
    }

    
    public function current(): Sheet
    {
        $escapedSheetName = $this->xmlReader->getAttribute(self::XML_ATTRIBUTE_TABLE_NAME);
        \assert(null !== $escapedSheetName);
        $sheetName = $this->escaper->unescape($escapedSheetName);

        $isSheetActive = $this->isSheetActive($sheetName, $this->currentSheetIndex, $this->activeSheetName);

        $sheetStyleName = $this->xmlReader->getAttribute(self::XML_ATTRIBUTE_TABLE_STYLE_NAME);
        \assert(null !== $sheetStyleName);
        $isSheetVisible = $this->isSheetVisible($sheetStyleName);

        return new Sheet(
            new RowIterator(
                $this->options,
                new CellValueFormatter($this->options->SHOULD_FORMAT_DATES, new ODS()),
                new XMLProcessor($this->xmlReader)
            ),
            $this->currentSheetIndex,
            $sheetName,
            $isSheetActive,
            $isSheetVisible
        );
    }

    
    public function key(): int
    {
        return $this->currentSheetIndex + 1;
    }

    
    private function readSheetsVisibility(): array
    {
        $sheetsVisibility = [];

        $this->xmlReader->readUntilNodeFound(self::XML_NODE_AUTOMATIC_STYLES);

        $automaticStylesNode = $this->xmlReader->expand();
        \assert($automaticStylesNode instanceof DOMElement);

        $tableStyleNodes = $automaticStylesNode->getElementsByTagNameNS(self::XML_STYLE_NAMESPACE, self::XML_NODE_STYLE_TABLE_PROPERTIES);

        foreach ($tableStyleNodes as $tableStyleNode) {
            $isSheetVisible = ('false' !== $tableStyleNode->getAttribute(self::XML_ATTRIBUTE_TABLE_DISPLAY));

            $parentStyleNode = $tableStyleNode->parentNode;
            \assert($parentStyleNode instanceof DOMElement);
            $styleName = $parentStyleNode->getAttribute(self::XML_ATTRIBUTE_STYLE_NAME);

            $sheetsVisibility[$styleName] = $isSheetVisible;
        }

        return $sheetsVisibility;
    }

    
    private function isSheetActive(string $sheetName, int $sheetIndex, ?string $activeSheetName): bool
    {
        
        
        return
            (null === $activeSheetName && 0 === $sheetIndex)
            || ($activeSheetName === $sheetName);
    }

    
    private function isSheetVisible(string $sheetStyleName): bool
    {
        return $this->sheetsVisibility[$sheetStyleName] ??
            true;
    }
}
