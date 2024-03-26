<?php

declare(strict_types=1);

namespace OpenSpout\Reader\ODS\Helper;

use DateInterval;
use DateTimeImmutable;
use DOMElement;
use DOMNode;
use DOMText;
use Exception;
use OpenSpout\Common\Helper\Escaper\ODS;
use OpenSpout\Reader\Exception\InvalidValueException;


final class CellValueFormatter
{
    
    public const CELL_TYPE_STRING = 'string';
    public const CELL_TYPE_FLOAT = 'float';
    public const CELL_TYPE_BOOLEAN = 'boolean';
    public const CELL_TYPE_DATE = 'date';
    public const CELL_TYPE_TIME = 'time';
    public const CELL_TYPE_CURRENCY = 'currency';
    public const CELL_TYPE_PERCENTAGE = 'percentage';
    public const CELL_TYPE_VOID = 'void';

    
    public const XML_NODE_P = 'p';
    public const XML_NODE_TEXT_A = 'text:a';
    public const XML_NODE_TEXT_SPAN = 'text:span';
    public const XML_NODE_TEXT_S = 'text:s';
    public const XML_NODE_TEXT_TAB = 'text:tab';
    public const XML_NODE_TEXT_LINE_BREAK = 'text:line-break';

    
    public const XML_ATTRIBUTE_TYPE = 'office:value-type';
    public const XML_ATTRIBUTE_VALUE = 'office:value';
    public const XML_ATTRIBUTE_BOOLEAN_VALUE = 'office:boolean-value';
    public const XML_ATTRIBUTE_DATE_VALUE = 'office:date-value';
    public const XML_ATTRIBUTE_TIME_VALUE = 'office:time-value';
    public const XML_ATTRIBUTE_CURRENCY = 'office:currency';
    public const XML_ATTRIBUTE_C = 'text:c';

    
    private const WHITESPACE_XML_NODES = [
        self::XML_NODE_TEXT_S => ' ',
        self::XML_NODE_TEXT_TAB => "\t",
        self::XML_NODE_TEXT_LINE_BREAK => "\n",
    ];

    
    private readonly bool $shouldFormatDates;

    
    private readonly ODS $escaper;

    
    public function __construct(bool $shouldFormatDates, ODS $escaper)
    {
        $this->shouldFormatDates = $shouldFormatDates;
        $this->escaper = $escaper;
    }

    
    public function extractAndFormatNodeValue(DOMElement $node): bool|DateInterval|DateTimeImmutable|float|int|string
    {
        $cellType = $node->getAttribute(self::XML_ATTRIBUTE_TYPE);

        return match ($cellType) {
            self::CELL_TYPE_STRING => $this->formatStringCellValue($node),
            self::CELL_TYPE_FLOAT => $this->formatFloatCellValue($node),
            self::CELL_TYPE_BOOLEAN => $this->formatBooleanCellValue($node),
            self::CELL_TYPE_DATE => $this->formatDateCellValue($node),
            self::CELL_TYPE_TIME => $this->formatTimeCellValue($node),
            self::CELL_TYPE_CURRENCY => $this->formatCurrencyCellValue($node),
            self::CELL_TYPE_PERCENTAGE => $this->formatPercentageCellValue($node),
            default => '',
        };
    }

    
    private function formatStringCellValue(DOMElement $node): string
    {
        $pNodeValues = [];
        $pNodes = $node->getElementsByTagName(self::XML_NODE_P);

        foreach ($pNodes as $pNode) {
            $pNodeValues[] = $this->extractTextValueFromNode($pNode);
        }

        $escapedCellValue = implode("\n", $pNodeValues);

        return $this->escaper->unescape($escapedCellValue);
    }

    
    private function formatFloatCellValue(DOMElement $node): float|int
    {
        $nodeValue = $node->getAttribute(self::XML_ATTRIBUTE_VALUE);

        $nodeIntValue = (int) $nodeValue;
        $nodeFloatValue = (float) $nodeValue;

        return ((float) $nodeIntValue === $nodeFloatValue) ? $nodeIntValue : $nodeFloatValue;
    }

    
    private function formatBooleanCellValue(DOMElement $node): bool
    {
        return (bool) $node->getAttribute(self::XML_ATTRIBUTE_BOOLEAN_VALUE);
    }

    
    private function formatDateCellValue(DOMElement $node): DateTimeImmutable|string
    {
        
        
        
        

        if ($this->shouldFormatDates) {
            
            $nodeWithValueAlreadyFormatted = $node->getElementsByTagName(self::XML_NODE_P)->item(0);
            $cellValue = $nodeWithValueAlreadyFormatted->nodeValue;
        } else {
            
            $nodeValue = $node->getAttribute(self::XML_ATTRIBUTE_DATE_VALUE);

            try {
                $cellValue = new DateTimeImmutable($nodeValue);
            } catch (Exception $previous) {
                throw new InvalidValueException($nodeValue, '', 0, $previous);
            }
        }

        return $cellValue;
    }

    
    private function formatTimeCellValue(DOMElement $node): DateInterval|string
    {
        
        
        
        

        if ($this->shouldFormatDates) {
            
            $nodeWithValueAlreadyFormatted = $node->getElementsByTagName(self::XML_NODE_P)->item(0);
            $cellValue = $nodeWithValueAlreadyFormatted->nodeValue;
        } else {
            
            $nodeValue = $node->getAttribute(self::XML_ATTRIBUTE_TIME_VALUE);

            try {
                $cellValue = new DateInterval($nodeValue);
            } catch (Exception $previous) {
                throw new InvalidValueException($nodeValue, '', 0, $previous);
            }
        }

        return $cellValue;
    }

    
    private function formatCurrencyCellValue(DOMElement $node): string
    {
        $value = $node->getAttribute(self::XML_ATTRIBUTE_VALUE);
        $currency = $node->getAttribute(self::XML_ATTRIBUTE_CURRENCY);

        return "{$value} {$currency}";
    }

    
    private function formatPercentageCellValue(DOMElement $node): float|int
    {
        
        return $this->formatFloatCellValue($node);
    }

    private function extractTextValueFromNode(DOMNode $pNode): string
    {
        $textValue = '';

        foreach ($pNode->childNodes as $childNode) {
            if ($childNode instanceof DOMText) {
                $textValue .= $childNode->nodeValue;
            } elseif ($this->isWhitespaceNode($childNode->nodeName) && $childNode instanceof DOMElement) {
                $textValue .= $this->transformWhitespaceNode($childNode);
            } elseif (self::XML_NODE_TEXT_A === $childNode->nodeName || self::XML_NODE_TEXT_SPAN === $childNode->nodeName) {
                $textValue .= $this->extractTextValueFromNode($childNode);
            }
        }

        return $textValue;
    }

    
    private function isWhitespaceNode(string $nodeName): bool
    {
        return isset(self::WHITESPACE_XML_NODES[$nodeName]);
    }

    
    private function transformWhitespaceNode(DOMElement $node): string
    {
        $countAttribute = $node->getAttribute(self::XML_ATTRIBUTE_C); 
        $numWhitespaces = '' !== $countAttribute ? (int) $countAttribute : 1;

        return str_repeat(self::WHITESPACE_XML_NODES[$node->nodeName], $numWhitespaces);
    }
}
