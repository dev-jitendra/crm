<?php

declare(strict_types=1);

namespace OpenSpout\Reader\XLSX\Manager;

use OpenSpout\Common\Exception\IOException;
use OpenSpout\Reader\Wrapper\XMLReader;


final class WorkbookRelationshipsManager
{
    public const BASE_PATH = 'xl/';

    
    public const WORKBOOK_RELS_XML_FILE_PATH = 'xl/_rels/workbook.xml.rels';

    
    public const RELATIONSHIP_TYPE_SHARED_STRINGS = 'http:
    public const RELATIONSHIP_TYPE_STYLES = 'http:
    public const RELATIONSHIP_TYPE_SHARED_STRINGS_STRICT = 'http:
    public const RELATIONSHIP_TYPE_STYLES_STRICT = 'http:

    
    public const XML_NODE_RELATIONSHIP = 'Relationship';
    public const XML_ATTRIBUTE_TYPE = 'Type';
    public const XML_ATTRIBUTE_TARGET = 'Target';

    
    private readonly string $filePath;

    
    private array $cachedWorkbookRelationships;

    
    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
    }

    
    public function getSharedStringsXMLFilePath(): string
    {
        $workbookRelationships = $this->getWorkbookRelationships();
        $sharedStringsXMLFilePath = $workbookRelationships[self::RELATIONSHIP_TYPE_SHARED_STRINGS]
            ?? $workbookRelationships[self::RELATIONSHIP_TYPE_SHARED_STRINGS_STRICT];

        
        $doesContainBasePath = str_contains($sharedStringsXMLFilePath, self::BASE_PATH);
        if (!$doesContainBasePath) {
            
            $sharedStringsXMLFilePath = self::BASE_PATH.$sharedStringsXMLFilePath;
        }

        return $sharedStringsXMLFilePath;
    }

    
    public function hasSharedStringsXMLFile(): bool
    {
        $workbookRelationships = $this->getWorkbookRelationships();

        return isset($workbookRelationships[self::RELATIONSHIP_TYPE_SHARED_STRINGS])
            || isset($workbookRelationships[self::RELATIONSHIP_TYPE_SHARED_STRINGS_STRICT]);
    }

    
    public function hasStylesXMLFile(): bool
    {
        $workbookRelationships = $this->getWorkbookRelationships();

        return isset($workbookRelationships[self::RELATIONSHIP_TYPE_STYLES])
            || isset($workbookRelationships[self::RELATIONSHIP_TYPE_STYLES_STRICT]);
    }

    
    public function getStylesXMLFilePath(): string
    {
        $workbookRelationships = $this->getWorkbookRelationships();
        $stylesXMLFilePath = $workbookRelationships[self::RELATIONSHIP_TYPE_STYLES]
            ?? $workbookRelationships[self::RELATIONSHIP_TYPE_STYLES_STRICT];

        
        $doesContainBasePath = str_contains($stylesXMLFilePath, self::BASE_PATH);
        if (!$doesContainBasePath) {
            
            $stylesXMLFilePath = self::BASE_PATH.$stylesXMLFilePath;
        }

        return $stylesXMLFilePath;
    }

    
    private function getWorkbookRelationships(): array
    {
        if (!isset($this->cachedWorkbookRelationships)) {
            $xmlReader = new XMLReader();

            if (false === $xmlReader->openFileInZip($this->filePath, self::WORKBOOK_RELS_XML_FILE_PATH)) {
                throw new IOException('Could not open "'.self::WORKBOOK_RELS_XML_FILE_PATH.'".');
            }

            $this->cachedWorkbookRelationships = [];

            while ($xmlReader->readUntilNodeFound(self::XML_NODE_RELATIONSHIP)) {
                $this->processWorkbookRelationship($xmlReader);
            }
        }

        return $this->cachedWorkbookRelationships;
    }

    
    private function processWorkbookRelationship(XMLReader $xmlReader): void
    {
        $type = $xmlReader->getAttribute(self::XML_ATTRIBUTE_TYPE);
        $target = $xmlReader->getAttribute(self::XML_ATTRIBUTE_TARGET);
        \assert(null !== $target);

        
        
        $this->cachedWorkbookRelationships[$type] = $target;
    }
}
