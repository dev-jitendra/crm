<?php

declare(strict_types=1);

namespace OpenSpout\Writer\CSV;

use OpenSpout\Common\Entity\Cell;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Exception\IOException;
use OpenSpout\Common\Helper\EncodingHelper;
use OpenSpout\Writer\AbstractWriter;

final class Writer extends AbstractWriter
{
    
    protected static string $headerContentType = 'text/csv; charset=UTF-8';

    private readonly Options $options;

    private int $lastWrittenRowIndex = 0;

    public function __construct(?Options $options = null)
    {
        $this->options = $options ?? new Options();
    }

    public function getOptions(): Options
    {
        return $this->options;
    }

    
    protected function openWriter(): void
    {
        if ($this->options->SHOULD_ADD_BOM) {
            
            fwrite($this->filePointer, EncodingHelper::BOM_UTF8);
        }
    }

    
    protected function addRowToWriter(Row $row): void
    {
        $cells = array_map(static function (Cell\BooleanCell|Cell\DateIntervalCell|Cell\DateTimeCell|Cell\EmptyCell|Cell\FormulaCell|Cell\NumericCell|Cell\StringCell $value): string {
            if ($value instanceof Cell\BooleanCell) {
                return (string) (int) $value->getValue();
            }
            if ($value instanceof Cell\DateTimeCell) {
                return $value->getValue()->format(DATE_ATOM);
            }
            if ($value instanceof Cell\DateIntervalCell) {
                return $value->getValue()->format('P%yY%mM%dDT%hH%iM%sS%fF');
            }

            return (string) $value->getValue();
        }, $row->getCells());

        $wasWriteSuccessful = fputcsv(
            $this->filePointer,
            $cells,
            $this->options->FIELD_DELIMITER,
            $this->options->FIELD_ENCLOSURE,
            ''
        );
        if (false === $wasWriteSuccessful) {
            throw new IOException('Unable to write data'); 
        }

        ++$this->lastWrittenRowIndex;
        if (0 === $this->lastWrittenRowIndex % $this->options->FLUSH_THRESHOLD) {
            fflush($this->filePointer);
        }
    }

    
    protected function closeWriter(): void
    {
        $this->lastWrittenRowIndex = 0;
    }
}
