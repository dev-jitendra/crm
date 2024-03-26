<?php

declare(strict_types=1);

namespace OpenSpout\Reader\XLSX\Manager;

use DOMElement;
use OpenSpout\Common\Exception\IOException;
use OpenSpout\Reader\Exception\XMLProcessingException;
use OpenSpout\Reader\Wrapper\XMLReader;
use OpenSpout\Reader\XLSX\Manager\SharedStringsCaching\CachingStrategyFactoryInterface;
use OpenSpout\Reader\XLSX\Manager\SharedStringsCaching\CachingStrategyInterface;
use OpenSpout\Reader\XLSX\Options;


final class SharedStringsManager
{
    
    public const XML_NODE_SST = 'sst';
    public const XML_NODE_SI = 'si';
    public const XML_NODE_R = 'r';
    public const XML_NODE_T = 't';

    
    public const XML_ATTRIBUTE_COUNT = 'count';
    public const XML_ATTRIBUTE_UNIQUE_COUNT = 'uniqueCount';
    public const XML_ATTRIBUTE_XML_SPACE = 'xml:space';
    public const XML_ATTRIBUTE_VALUE_PRESERVE = 'preserve';

    
    private readonly string $filePath;

    private readonly Options $options;

    
    private readonly WorkbookRelationshipsManager $workbookRelationshipsManager;

    
    private readonly CachingStrategyFactoryInterface $cachingStrategyFactory;

    
    private CachingStrategyInterface $cachingStrategy;

    public function __construct(
        string $filePath,
        Options $options,
        WorkbookRelationshipsManager $workbookRelationshipsManager,
        CachingStrategyFactoryInterface $cachingStrategyFactory
    ) {
        $this->filePath = $filePath;
        $this->options = $options;
        $this->workbookRelationshipsManager = $workbookRelationshipsManager;
        $this->cachingStrategyFactory = $cachingStrategyFactory;
    }

    
    public function hasSharedStrings(): bool
    {
        return $this->workbookRelationshipsManager->hasSharedStringsXMLFile();
    }

    
    public function extractSharedStrings(): void
    {
        $sharedStringsXMLFilePath = $this->workbookRelationshipsManager->getSharedStringsXMLFilePath();
        $xmlReader = new XMLReader();
        $sharedStringIndex = 0;

        if (false === $xmlReader->openFileInZip($this->filePath, $sharedStringsXMLFilePath)) {
            throw new IOException('Could not open "'.$sharedStringsXMLFilePath.'".');
        }

        try {
            $sharedStringsUniqueCount = $this->getSharedStringsUniqueCount($xmlReader);
            $this->cachingStrategy = $this->getBestSharedStringsCachingStrategy($sharedStringsUniqueCount);

            $xmlReader->readUntilNodeFound(self::XML_NODE_SI);

            while (self::XML_NODE_SI === $xmlReader->getCurrentNodeName()) {
                $this->processSharedStringsItem($xmlReader, $sharedStringIndex);
                ++$sharedStringIndex;

                
                $xmlReader->next(self::XML_NODE_SI);
            }

            $this->cachingStrategy->closeCache();
        } catch (XMLProcessingException $exception) {
            throw new IOException("The sharedStrings.xml file is invalid and cannot be read. [{$exception->getMessage()}]");
        }

        $xmlReader->close();
    }

    
    public function getStringAtIndex(int $sharedStringIndex): string
    {
        return $this->cachingStrategy->getStringAtIndex($sharedStringIndex);
    }

    
    public function cleanup(): void
    {
        if (isset($this->cachingStrategy)) {
            $this->cachingStrategy->clearCache();
        }
    }

    
    private function getSharedStringsUniqueCount(XMLReader $xmlReader): ?int
    {
        $xmlReader->next(self::XML_NODE_SST);

        
        while (self::XML_NODE_SST === $xmlReader->getCurrentNodeName() && XMLReader::ELEMENT !== $xmlReader->nodeType) {
            $xmlReader->read();
        }

        $uniqueCount = $xmlReader->getAttribute(self::XML_ATTRIBUTE_UNIQUE_COUNT);

        
        
        if (null === $uniqueCount) {
            $uniqueCount = $xmlReader->getAttribute(self::XML_ATTRIBUTE_COUNT);
        }

        return (null !== $uniqueCount) ? (int) $uniqueCount : null;
    }

    
    private function getBestSharedStringsCachingStrategy(?int $sharedStringsUniqueCount): CachingStrategyInterface
    {
        return $this->cachingStrategyFactory
            ->createBestCachingStrategy($sharedStringsUniqueCount, $this->options->getTempFolder())
        ;
    }

    
    private function processSharedStringsItem(XMLReader $xmlReader, int $sharedStringIndex): void
    {
        $sharedStringValue = '';

        
        $siNode = $xmlReader->expand();
        \assert($siNode instanceof DOMElement);
        $textNodes = $siNode->getElementsByTagName(self::XML_NODE_T);

        foreach ($textNodes as $textNode) {
            if ($this->shouldExtractTextNodeValue($textNode)) {
                $textNodeValue = $textNode->nodeValue;
                \assert(null !== $textNodeValue);
                $shouldPreserveWhitespace = $this->shouldPreserveWhitespace($textNode);

                $sharedStringValue .= $shouldPreserveWhitespace
                    ? $textNodeValue
                    : trim($textNodeValue);
            }
        }

        $this->cachingStrategy->addStringForIndex($sharedStringValue, $sharedStringIndex);
    }

    
    private function shouldExtractTextNodeValue(DOMElement $textNode): bool
    {
        $parentNode = $textNode->parentNode;
        \assert(null !== $parentNode);
        $parentTagName = $parentNode->localName;

        return self::XML_NODE_SI === $parentTagName || self::XML_NODE_R === $parentTagName;
    }

    
    private function shouldPreserveWhitespace(DOMElement $textNode): bool
    {
        $spaceValue = $textNode->getAttribute(self::XML_ATTRIBUTE_XML_SPACE);

        return self::XML_ATTRIBUTE_VALUE_PRESERVE === $spaceValue;
    }
}
