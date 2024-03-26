<?php

declare(strict_types=1);

namespace OpenSpout\Reader\XLSX\Manager;

use OpenSpout\Reader\Wrapper\XMLReader;

class StyleManager implements StyleManagerInterface
{
    
    final public const XML_NODE_NUM_FMTS = 'numFmts';
    final public const XML_NODE_NUM_FMT = 'numFmt';
    final public const XML_NODE_CELL_XFS = 'cellXfs';
    final public const XML_NODE_XF = 'xf';

    
    final public const XML_ATTRIBUTE_NUM_FMT_ID = 'numFmtId';
    final public const XML_ATTRIBUTE_FORMAT_CODE = 'formatCode';
    final public const XML_ATTRIBUTE_APPLY_NUMBER_FORMAT = 'applyNumberFormat';
    final public const XML_ATTRIBUTE_COUNT = 'count';

    
    final public const DEFAULT_STYLE_ID = 0;

    final public const NUMBER_FORMAT_GENERAL = 'General';

    
    private const builtinNumFmtIdToNumFormatMapping = [
        14 => 'm/d/yyyy', 
        15 => 'd-mmm-yy',
        16 => 'd-mmm',
        17 => 'mmm-yy',
        18 => 'h:mm AM/PM',
        19 => 'h:mm:ss AM/PM',
        20 => 'h:mm',
        21 => 'h:mm:ss',
        22 => 'm/d/yyyy h:mm', 
        45 => 'mm:ss',
        46 => '[h]:mm:ss',
        47 => 'mm:ss.0',  
    ];

    
    private readonly string $filePath;

    
    private readonly ?string $stylesXMLFilePath;

    
    private array $customNumberFormats;

    
    private array $stylesAttributes;

    
    private array $numFmtIdToIsDateFormatCache = [];

    
    public function __construct(string $filePath, ?string $stylesXMLFilePath)
    {
        $this->filePath = $filePath;
        $this->stylesXMLFilePath = $stylesXMLFilePath;
    }

    public function shouldFormatNumericValueAsDate(int $styleId): bool
    {
        if (null === $this->stylesXMLFilePath) {
            return false;
        }

        $stylesAttributes = $this->getStylesAttributes();

        
        
        
        if (self::DEFAULT_STYLE_ID === $styleId || !isset($stylesAttributes[$styleId])) {
            return false;
        }

        $styleAttributes = $stylesAttributes[$styleId];

        return $this->doesStyleIndicateDate($styleAttributes);
    }

    public function getNumberFormatCode(int $styleId): string
    {
        $stylesAttributes = $this->getStylesAttributes();
        $styleAttributes = $stylesAttributes[$styleId];
        $numFmtId = $styleAttributes[self::XML_ATTRIBUTE_NUM_FMT_ID];
        \assert(\is_int($numFmtId));

        if ($this->isNumFmtIdBuiltInDateFormat($numFmtId)) {
            $numberFormatCode = self::builtinNumFmtIdToNumFormatMapping[$numFmtId];
        } else {
            $customNumberFormats = $this->getCustomNumberFormats();
            $numberFormatCode = $customNumberFormats[$numFmtId] ?? '';
        }

        return $numberFormatCode;
    }

    
    protected function getCustomNumberFormats(): array
    {
        if (!isset($this->customNumberFormats)) {
            $this->extractRelevantInfo();
        }

        return $this->customNumberFormats;
    }

    
    protected function getStylesAttributes(): array
    {
        if (!isset($this->stylesAttributes)) {
            $this->extractRelevantInfo();
        }

        return $this->stylesAttributes;
    }

    
    private function extractRelevantInfo(): void
    {
        $this->customNumberFormats = [];
        $this->stylesAttributes = [];

        $xmlReader = new XMLReader();

        if ($xmlReader->openFileInZip($this->filePath, $this->stylesXMLFilePath)) {
            while ($xmlReader->read()) {
                if ($xmlReader->isPositionedOnStartingNode(self::XML_NODE_NUM_FMTS)
                    && '0' !== $xmlReader->getAttribute(self::XML_ATTRIBUTE_COUNT)) {
                    $this->extractNumberFormats($xmlReader);
                } elseif ($xmlReader->isPositionedOnStartingNode(self::XML_NODE_CELL_XFS)) {
                    $this->extractStyleAttributes($xmlReader);
                }
            }

            $xmlReader->close();
        }
    }

    
    private function extractNumberFormats(XMLReader $xmlReader): void
    {
        while ($xmlReader->read()) {
            if ($xmlReader->isPositionedOnStartingNode(self::XML_NODE_NUM_FMT)) {
                $numFmtId = (int) $xmlReader->getAttribute(self::XML_ATTRIBUTE_NUM_FMT_ID);
                $formatCode = $xmlReader->getAttribute(self::XML_ATTRIBUTE_FORMAT_CODE);
                \assert(null !== $formatCode);
                $this->customNumberFormats[$numFmtId] = $formatCode;
            } elseif ($xmlReader->isPositionedOnEndingNode(self::XML_NODE_NUM_FMTS)) {
                
                break;
            }
        }
    }

    
    private function extractStyleAttributes(XMLReader $xmlReader): void
    {
        while ($xmlReader->read()) {
            if ($xmlReader->isPositionedOnStartingNode(self::XML_NODE_XF)) {
                $numFmtId = $xmlReader->getAttribute(self::XML_ATTRIBUTE_NUM_FMT_ID);
                $normalizedNumFmtId = (null !== $numFmtId) ? (int) $numFmtId : null;

                $applyNumberFormat = $xmlReader->getAttribute(self::XML_ATTRIBUTE_APPLY_NUMBER_FORMAT);
                $normalizedApplyNumberFormat = (null !== $applyNumberFormat) ? (bool) $applyNumberFormat : null;

                $this->stylesAttributes[] = [
                    self::XML_ATTRIBUTE_NUM_FMT_ID => $normalizedNumFmtId,
                    self::XML_ATTRIBUTE_APPLY_NUMBER_FORMAT => $normalizedApplyNumberFormat,
                ];
            } elseif ($xmlReader->isPositionedOnEndingNode(self::XML_NODE_CELL_XFS)) {
                
                break;
            }
        }
    }

    
    private function doesStyleIndicateDate(array $styleAttributes): bool
    {
        $applyNumberFormat = $styleAttributes[self::XML_ATTRIBUTE_APPLY_NUMBER_FORMAT];
        $numFmtId = $styleAttributes[self::XML_ATTRIBUTE_NUM_FMT_ID];

        
        
        
        
        
        if (false === $applyNumberFormat || !\is_int($numFmtId)) {
            return false;
        }

        return $this->doesNumFmtIdIndicateDate($numFmtId);
    }

    
    private function doesNumFmtIdIndicateDate(int $numFmtId): bool
    {
        if (!isset($this->numFmtIdToIsDateFormatCache[$numFmtId])) {
            $formatCode = $this->getFormatCodeForNumFmtId($numFmtId);

            $this->numFmtIdToIsDateFormatCache[$numFmtId] = (
                $this->isNumFmtIdBuiltInDateFormat($numFmtId)
                || $this->isFormatCodeCustomDateFormat($formatCode)
            );
        }

        return $this->numFmtIdToIsDateFormatCache[$numFmtId];
    }

    
    private function getFormatCodeForNumFmtId(int $numFmtId): ?string
    {
        $customNumberFormats = $this->getCustomNumberFormats();

        
        return $customNumberFormats[$numFmtId] ?? null;
    }

    
    private function isNumFmtIdBuiltInDateFormat(int $numFmtId): bool
    {
        return \array_key_exists($numFmtId, self::builtinNumFmtIdToNumFormatMapping);
    }

    
    private function isFormatCodeCustomDateFormat(?string $formatCode): bool
    {
        
        if (null === $formatCode || 0 === strcasecmp($formatCode, self::NUMBER_FORMAT_GENERAL)) {
            return false;
        }

        return $this->isFormatCodeMatchingDateFormatPattern($formatCode);
    }

    
    private function isFormatCodeMatchingDateFormatPattern(string $formatCode): bool
    {
        
        $pattern = '((?<!\\\)\[.+?(?<!\\\)\])';
        $formatCode = preg_replace($pattern, '', $formatCode);
        \assert(null !== $formatCode);

        
        $formatCode = preg_replace('/"[^"]+"/', '', $formatCode);
        \assert(null !== $formatCode);

        
        
        
        $dateFormatCharacters = ['e', 'yy', 'm', 'd', 'h', 's'];

        $hasFoundDateFormatCharacter = false;
        foreach ($dateFormatCharacters as $dateFormatCharacter) {
            
            $pattern = '/(?<!\\\)'.$dateFormatCharacter.'/i';

            if (1 === preg_match($pattern, $formatCode)) {
                $hasFoundDateFormatCharacter = true;

                break;
            }
        }

        return $hasFoundDateFormatCharacter;
    }
}
