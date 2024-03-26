<?php

declare(strict_types=1);

namespace OpenSpout\Writer\Common\Manager\Style;

use OpenSpout\Common\Entity\Style\Style;


final class StyleMerger
{
    
    public function merge(Style $style, Style $baseStyle): Style
    {
        $mergedStyle = clone $style;

        $this->mergeFontStyles($mergedStyle, $style, $baseStyle);
        $this->mergeOtherFontProperties($mergedStyle, $style, $baseStyle);
        $this->mergeCellProperties($mergedStyle, $style, $baseStyle);

        return $mergedStyle;
    }

    
    private function mergeFontStyles(Style $styleToUpdate, Style $style, Style $baseStyle): void
    {
        if (!$style->hasSetFontBold() && $baseStyle->isFontBold()) {
            $styleToUpdate->setFontBold();
        }
        if (!$style->hasSetFontItalic() && $baseStyle->isFontItalic()) {
            $styleToUpdate->setFontItalic();
        }
        if (!$style->hasSetFontUnderline() && $baseStyle->isFontUnderline()) {
            $styleToUpdate->setFontUnderline();
        }
        if (!$style->hasSetFontStrikethrough() && $baseStyle->isFontStrikethrough()) {
            $styleToUpdate->setFontStrikethrough();
        }
    }

    
    private function mergeOtherFontProperties(Style $styleToUpdate, Style $style, Style $baseStyle): void
    {
        if (!$style->hasSetFontSize() && Style::DEFAULT_FONT_SIZE !== $baseStyle->getFontSize()) {
            $styleToUpdate->setFontSize($baseStyle->getFontSize());
        }
        if (!$style->hasSetFontColor() && Style::DEFAULT_FONT_COLOR !== $baseStyle->getFontColor()) {
            $styleToUpdate->setFontColor($baseStyle->getFontColor());
        }
        if (!$style->hasSetFontName() && Style::DEFAULT_FONT_NAME !== $baseStyle->getFontName()) {
            $styleToUpdate->setFontName($baseStyle->getFontName());
        }
    }

    
    private function mergeCellProperties(Style $styleToUpdate, Style $style, Style $baseStyle): void
    {
        if (!$style->hasSetWrapText() && $baseStyle->hasSetWrapText()) {
            $styleToUpdate->setShouldWrapText($baseStyle->shouldWrapText());
        }
        if (!$style->hasSetShrinkToFit() && $baseStyle->shouldShrinkToFit()) {
            $styleToUpdate->setShouldShrinkToFit();
        }
        if (!$style->hasSetCellAlignment() && $baseStyle->shouldApplyCellAlignment()) {
            $styleToUpdate->setCellAlignment($baseStyle->getCellAlignment());
        }
        if (!$style->hasSetCellVerticalAlignment() && $baseStyle->shouldApplyCellVerticalAlignment()) {
            $styleToUpdate->setCellVerticalAlignment($baseStyle->getCellVerticalAlignment());
        }
        if (null === $style->getBorder() && null !== ($border = $baseStyle->getBorder())) {
            $styleToUpdate->setBorder($border);
        }
        if (null === $style->getFormat() && null !== ($format = $baseStyle->getFormat())) {
            $styleToUpdate->setFormat($format);
        }
        if (null === $style->getBackgroundColor() && null !== ($bgColor = $baseStyle->getBackgroundColor())) {
            $styleToUpdate->setBackgroundColor($bgColor);
        }
    }
}
