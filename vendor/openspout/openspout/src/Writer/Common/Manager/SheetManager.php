<?php

declare(strict_types=1);

namespace OpenSpout\Writer\Common\Manager;

use OpenSpout\Common\Helper\StringHelper;
use OpenSpout\Writer\Common\Entity\Sheet;
use OpenSpout\Writer\Exception\InvalidSheetNameException;


final class SheetManager
{
    
    public const MAX_LENGTH_SHEET_NAME = 31;

    
    private const INVALID_CHARACTERS_IN_SHEET_NAME = ['\\', '/', '?', '*', ':', '[', ']'];

    
    private static array $SHEETS_NAME_USED = [];

    private readonly StringHelper $stringHelper;

    
    public function __construct(StringHelper $stringHelper)
    {
        $this->stringHelper = $stringHelper;
    }

    
    public function throwIfNameIsInvalid(string $name, Sheet $sheet): void
    {
        $failedRequirements = [];
        $nameLength = $this->stringHelper->getStringLength($name);

        if (!$this->isNameUnique($name, $sheet)) {
            $failedRequirements[] = 'It should be unique';
        } elseif (0 === $nameLength) {
            $failedRequirements[] = 'It should not be blank';
        } else {
            if ($nameLength > self::MAX_LENGTH_SHEET_NAME) {
                $failedRequirements[] = 'It should not exceed 31 characters';
            }

            if ($this->doesContainInvalidCharacters($name)) {
                $failedRequirements[] = 'It should not contain these characters: \\ / ? * : [ or ]';
            }

            if ($this->doesStartOrEndWithSingleQuote($name)) {
                $failedRequirements[] = 'It should not start or end with a single quote';
            }
        }

        if (0 !== \count($failedRequirements)) {
            $errorMessage = "The sheet's name (\"{$name}\") is invalid. It did not respect these rules:\n - ";
            $errorMessage .= implode("\n - ", $failedRequirements);

            throw new InvalidSheetNameException($errorMessage);
        }
    }

    
    public function markWorkbookIdAsUsed(string $workbookId): void
    {
        if (!isset(self::$SHEETS_NAME_USED[$workbookId])) {
            self::$SHEETS_NAME_USED[$workbookId] = [];
        }
    }

    public function markSheetNameAsUsed(Sheet $sheet): void
    {
        self::$SHEETS_NAME_USED[$sheet->getAssociatedWorkbookId()][$sheet->getIndex()] = $sheet->getName();
    }

    
    private function doesContainInvalidCharacters(string $name): bool
    {
        return str_replace(self::INVALID_CHARACTERS_IN_SHEET_NAME, '', $name) !== $name;
    }

    
    private function doesStartOrEndWithSingleQuote(string $name): bool
    {
        $startsWithSingleQuote = (0 === $this->stringHelper->getCharFirstOccurrencePosition('\'', $name));
        $endsWithSingleQuote = ($this->stringHelper->getCharLastOccurrencePosition('\'', $name) === ($this->stringHelper->getStringLength($name) - 1));

        return $startsWithSingleQuote || $endsWithSingleQuote;
    }

    
    private function isNameUnique(string $name, Sheet $sheet): bool
    {
        foreach (self::$SHEETS_NAME_USED[$sheet->getAssociatedWorkbookId()] as $sheetIndex => $sheetName) {
            if ($sheetIndex !== $sheet->getIndex() && $sheetName === $name) {
                return false;
            }
        }

        return true;
    }
}
