<?php

declare(strict_types=1);

namespace OpenSpout\Writer\Common\Manager\Style;

use OpenSpout\Common\Entity\Cell;
use OpenSpout\Common\Entity\Style\Style;


abstract class AbstractStyleManager implements StyleManagerInterface
{
    
    protected AbstractStyleRegistry $styleRegistry;

    public function __construct(AbstractStyleRegistry $styleRegistry)
    {
        $this->styleRegistry = $styleRegistry;
    }

    
    final public function registerStyle(Style $style): Style
    {
        return $this->styleRegistry->registerStyle($style);
    }

    
    final public function applyExtraStylesIfNeeded(Cell $cell): PossiblyUpdatedStyle
    {
        return $this->applyWrapTextIfCellContainsNewLine($cell);
    }

    
    final protected function getDefaultStyle(): Style
    {
        
        return $this->styleRegistry->getRegisteredStyles()[0];
    }

    
    private function applyWrapTextIfCellContainsNewLine(Cell $cell): PossiblyUpdatedStyle
    {
        $cellStyle = $cell->getStyle();

        
        if (!$cellStyle->hasSetWrapText() && $cell instanceof Cell\StringCell && str_contains($cell->getValue(), "\n")) {
            $cellStyle->setShouldWrapText();

            return new PossiblyUpdatedStyle($cellStyle, true);
        }

        return new PossiblyUpdatedStyle($cellStyle, false);
    }
}
