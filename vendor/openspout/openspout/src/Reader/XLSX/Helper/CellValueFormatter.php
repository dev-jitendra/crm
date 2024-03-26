<?php

declare(strict_types=1);

namespace OpenSpout\Reader\XLSX\Helper;

use DateInterval;
use DateTimeImmutable;
use DOMElement;
use Exception;
use OpenSpout\Common\Entity\Cell;
use OpenSpout\Common\Helper\Escaper\XLSX;
use OpenSpout\Reader\Exception\InvalidValueException;
use OpenSpout\Reader\XLSX\Manager\SharedStringsManager;
use OpenSpout\Reader\XLSX\Manager\StyleManagerInterface;


final class CellValueFormatter
{
    
    public const CELL_TYPE_INLINE_STRING = 'inlineStr';
    public const CELL_TYPE_STR = 'str';
    public const CELL_TYPE_SHARED_STRING = 's';
    public const CELL_TYPE_BOOLEAN = 'b';
    public const CELL_TYPE_NUMERIC = 'n';
    public const CELL_TYPE_DATE = 'd';
    public const CELL_TYPE_ERROR = 'e';

    
    public const XML_NODE_VALUE = 'v';
    public const XML_NODE_INLINE_STRING_VALUE = 't';
    public const XML_NODE_FORMULA = 'f';

    
    public const XML_ATTRIBUTE_TYPE = 't';
    public const XML_ATTRIBUTE_STYLE_ID = 's';

    
    public const NUM_SECONDS_IN_ONE_DAY = 86400;

    
    private readonly SharedStringsManager $sharedStringsManager;

    
    private readonly StyleManagerInterface $styleManager;

    
    private readonly bool $shouldFormatDates;

    
    private readonly bool $shouldUse1904Dates;

    
    private readonly XLSX $escaper;

    
    public function __construct(
        SharedStringsManager $sharedStringsManager,
        StyleManagerInterface $styleManager,
        bool $shouldFormatDates,
        bool $shouldUse1904Dates,
        XLSX $escaper
    ) {
        $this->sharedStringsManager = $sharedStringsManager;
        $this->styleManager = $styleManager;
        $this->shouldFormatDates = $shouldFormatDates;
        $this->shouldUse1904Dates = $shouldUse1904Dates;
        $this->escaper = $escaper;
    }

    
    public function extractAndFormatNodeValue(DOMElement $node): Cell
    {
        
        $cellType = $node->getAttribute(self::XML_ATTRIBUTE_TYPE);
        if ('' === $cellType) {
            $cellType = self::CELL_TYPE_NUMERIC;
        }
        $vNodeValue = $this->getVNodeValue($node);

        if (self::CELL_TYPE_NUMERIC === $cellType) {
            $fNodeValue = $node->getElementsByTagName(self::XML_NODE_FORMULA)->item(0)?->nodeValue;
            if (null !== $fNodeValue) {
                $computedValue = $this->formatNumericCellValue($vNodeValue, (int) $node->getAttribute(self::XML_ATTRIBUTE_STYLE_ID));

                return new Cell\FormulaCell('='.$fNodeValue, null, $computedValue);
            }
        }

        if ('' === $vNodeValue && self::CELL_TYPE_INLINE_STRING !== $cellType) {
            return Cell::fromValue($vNodeValue);
        }

        $rawValue = match ($cellType) {
            self::CELL_TYPE_INLINE_STRING => $this->formatInlineStringCellValue($node),
            self::CELL_TYPE_SHARED_STRING => $this->formatSharedStringCellValue($vNodeValue),
            self::CELL_TYPE_STR => $this->formatStrCellValue($vNodeValue),
            self::CELL_TYPE_BOOLEAN => $this->formatBooleanCellValue($vNodeValue),
            self::CELL_TYPE_NUMERIC => $this->formatNumericCellValue($vNodeValue, (int) $node->getAttribute(self::XML_ATTRIBUTE_STYLE_ID)),
            self::CELL_TYPE_DATE => $this->formatDateCellValue($vNodeValue),
            default => new Cell\ErrorCell($vNodeValue, null),
        };

        if ($rawValue instanceof Cell) {
            return $rawValue;
        }

        return Cell::fromValue($rawValue);
    }

    
    private function getVNodeValue(DOMElement $node): string
    {
        
        
        $vNode = $node->getElementsByTagName(self::XML_NODE_VALUE)->item(0);

        return (string) $vNode?->nodeValue;
    }

    
    private function formatInlineStringCellValue(DOMElement $node): string
    {
        
        
        $tNodes = $node->getElementsByTagName(self::XML_NODE_INLINE_STRING_VALUE);

        $cellValue = '';
        for ($i = 0; $i < $tNodes->count(); ++$i) {
            $nodeValue = $tNodes->item($i)->nodeValue;
            \assert(null !== $nodeValue);
            $cellValue .= $this->escaper->unescape($nodeValue);
        }

        return $cellValue;
    }

    
    private function formatSharedStringCellValue(string $nodeValue): string
    {
        
        
        $sharedStringIndex = (int) $nodeValue;
        $escapedCellValue = $this->sharedStringsManager->getStringAtIndex($sharedStringIndex);

        return $this->escaper->unescape($escapedCellValue);
    }

    
    private function formatStrCellValue(string $nodeValue): string
    {
        $escapedCellValue = trim($nodeValue);

        return $this->escaper->unescape($escapedCellValue);
    }

    
    private function formatNumericCellValue(float|int|string $nodeValue, int $cellStyleId): DateInterval|DateTimeImmutable|float|int|string
    {
        
        
        $formatCode = $this->styleManager->getNumberFormatCode($cellStyleId);

        if (DateIntervalFormatHelper::isDurationFormat($formatCode)) {
            $cellValue = $this->formatExcelDateIntervalValue((float) $nodeValue, $formatCode);
        } elseif ($this->styleManager->shouldFormatNumericValueAsDate($cellStyleId)) {
            $cellValue = $this->formatExcelTimestampValue((float) $nodeValue, $cellStyleId);
        } else {
            $nodeIntValue = (int) $nodeValue;
            $nodeFloatValue = (float) $nodeValue;
            $cellValue = ((float) $nodeIntValue === $nodeFloatValue) ? $nodeIntValue : $nodeFloatValue;
        }

        return $cellValue;
    }

    private function formatExcelDateIntervalValue(float $nodeValue, string $excelFormat): DateInterval|string
    {
        $dateInterval = DateIntervalFormatHelper::createDateIntervalFromHours($nodeValue);
        if ($this->shouldFormatDates) {
            return DateIntervalFormatHelper::formatDateInterval($dateInterval, $excelFormat);
        }

        return $dateInterval;
    }

    
    private function formatExcelTimestampValue(float $nodeValue, int $cellStyleId): DateTimeImmutable|string
    {
        if (!$this->isValidTimestampValue($nodeValue)) {
            throw new InvalidValueException((string) $nodeValue);
        }

        return $this->formatExcelTimestampValueAsDateTimeValue($nodeValue, $cellStyleId);
    }

    
    private function isValidTimestampValue(float $timestampValue): bool
    {
        
        return
            $this->shouldUse1904Dates && $timestampValue >= -695055 && $timestampValue <= 2957003.9999884
            || !$this->shouldUse1904Dates && $timestampValue >= -693593 && $timestampValue <= 2958465.9999884;
    }

    
    private function formatExcelTimestampValueAsDateTimeValue(float $nodeValue, int $cellStyleId): DateTimeImmutable|string
    {
        $baseDate = $this->shouldUse1904Dates ? '1904-01-01' : '1899-12-30';

        $daysSinceBaseDate = (int) $nodeValue;
        $daysSign = '+';
        if ($daysSinceBaseDate < 0) {
            $daysSinceBaseDate = abs($daysSinceBaseDate);
            $daysSign = '-';
        }
        $timeRemainder = fmod($nodeValue, 1);
        $secondsRemainder = round($timeRemainder * self::NUM_SECONDS_IN_ONE_DAY, 0);
        $secondsSign = '+';
        if ($secondsRemainder < 0) {
            $secondsRemainder = abs($secondsRemainder);
            $secondsSign = '-';
        }

        $dateObj = DateTimeImmutable::createFromFormat('|Y-m-d', $baseDate);
        \assert(false !== $dateObj);
        $dateObj = $dateObj->modify($daysSign.$daysSinceBaseDate.'days');
        \assert(false !== $dateObj);
        $dateObj = $dateObj->modify($secondsSign.$secondsRemainder.'seconds');
        \assert(false !== $dateObj);

        if ($this->shouldFormatDates) {
            $styleNumberFormatCode = $this->styleManager->getNumberFormatCode($cellStyleId);
            $phpDateFormat = DateFormatHelper::toPHPDateFormat($styleNumberFormatCode);
            $cellValue = $dateObj->format($phpDateFormat);
        } else {
            $cellValue = $dateObj;
        }

        return $cellValue;
    }

    
    private function formatBooleanCellValue(string $nodeValue): bool
    {
        return (bool) $nodeValue;
    }

    
    private function formatDateCellValue(string $nodeValue): Cell\ErrorCell|DateTimeImmutable|string
    {
        
        try {
            $cellValue = ($this->shouldFormatDates) ? $nodeValue : new DateTimeImmutable($nodeValue);
        } catch (Exception) {
            return new Cell\ErrorCell($nodeValue, null);
        }

        return $cellValue;
    }
}
