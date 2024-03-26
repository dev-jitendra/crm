<?php

declare(strict_types=1);

namespace OpenSpout\Writer\ODS\Manager\Style;

use OpenSpout\Common\Entity\Style\Border;
use OpenSpout\Common\Entity\Style\BorderPart;
use OpenSpout\Common\Entity\Style\CellAlignment;
use OpenSpout\Common\Entity\Style\CellVerticalAlignment;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\Common\AbstractOptions;
use OpenSpout\Writer\Common\ColumnWidth;
use OpenSpout\Writer\Common\Entity\Worksheet;
use OpenSpout\Writer\Common\Manager\Style\AbstractStyleManager as CommonStyleManager;
use OpenSpout\Writer\ODS\Helper\BorderHelper;


final class StyleManager extends CommonStyleManager
{
    private readonly AbstractOptions $options;

    public function __construct(StyleRegistry $styleRegistry, AbstractOptions $options)
    {
        parent::__construct($styleRegistry);
        $this->options = $options;
    }

    
    public function getStylesXMLFileContent(int $numWorksheets): string
    {
        $content = <<<'EOD'
            <?xml version="1.0" encoding="UTF-8" standalone="yes"?>
            <office:document-styles office:version="1.2" xmlns:dc="http:
            EOD;

        $content .= $this->getFontFaceSectionContent();
        $content .= $this->getStylesSectionContent();
        $content .= $this->getAutomaticStylesSectionContent($numWorksheets);
        $content .= $this->getMasterStylesSectionContent($numWorksheets);

        $content .= <<<'EOD'
            </office:document-styles>
            EOD;

        return $content;
    }

    
    public function getContentXmlFontFaceSectionContent(): string
    {
        $content = '<office:font-face-decls>';
        foreach ($this->styleRegistry->getUsedFonts() as $fontName) {
            $content .= '<style:font-face style:name="'.$fontName.'" svg:font-family="'.$fontName.'"/>';
        }
        $content .= '</office:font-face-decls>';

        return $content;
    }

    
    public function getContentXmlAutomaticStylesSectionContent(array $worksheets): string
    {
        $content = '<office:automatic-styles>';

        foreach ($this->styleRegistry->getRegisteredStyles() as $style) {
            $content .= $this->getStyleSectionContent($style);
        }

        $useOptimalRowHeight = null === $this->options->DEFAULT_ROW_HEIGHT ? 'true' : 'false';
        $defaultRowHeight = null === $this->options->DEFAULT_ROW_HEIGHT ? '15pt' : "{$this->options->DEFAULT_ROW_HEIGHT}pt";
        $defaultColumnWidth = null === $this->options->DEFAULT_COLUMN_WIDTH ? '' : "style:column-width=\"{$this->options->DEFAULT_COLUMN_WIDTH}pt\"";

        $content .= <<<EOD
            <style:style style:family="table-column" style:name="default-column-style">
                <style:table-column-properties fo:break-before="auto" {$defaultColumnWidth}/>
            </style:style>
            <style:style style:family="table-row" style:name="ro1">
                <style:table-row-properties fo:break-before="auto" style:row-height="{$defaultRowHeight}" style:use-optimal-row-height="{$useOptimalRowHeight}"/>
            </style:style>
            EOD;

        foreach ($worksheets as $worksheet) {
            $worksheetId = $worksheet->getId();
            $isSheetVisible = $worksheet->getExternalSheet()->isVisible() ? 'true' : 'false';

            $content .= <<<EOD
                <style:style style:family="table" style:master-page-name="mp{$worksheetId}" style:name="ta{$worksheetId}">
                    <style:table-properties style:writing-mode="lr-tb" table:display="{$isSheetVisible}"/>
                </style:style>
                EOD;
        }

        
        $columnWidths = $this->options->getColumnWidths();
        usort($columnWidths, static function (ColumnWidth $a, ColumnWidth $b): int {
            return $a->start <=> $b->start;
        });
        $content .= $this->getTableColumnStylesXMLContent();

        $content .= '</office:automatic-styles>';

        return $content;
    }

    public function getTableColumnStylesXMLContent(): string
    {
        if ([] === $this->options->getColumnWidths()) {
            return '';
        }

        $content = '';
        foreach ($this->options->getColumnWidths() as $styleIndex => $columnWidth) {
            $content .= <<<EOD
                <style:style style:family="table-column" style:name="co{$styleIndex}">
                    <style:table-column-properties fo:break-before="auto" style:use-optimal-column-width="false" style:column-width="{$columnWidth->width}pt"/>
                </style:style>
                EOD;
        }

        return $content;
    }

    public function getStyledTableColumnXMLContent(int $maxNumColumns): string
    {
        if ([] === $this->options->getColumnWidths()) {
            return '';
        }

        $content = '';
        foreach ($this->options->getColumnWidths() as $styleIndex => $columnWidth) {
            $numCols = $columnWidth->end - $columnWidth->start + 1;
            $content .= <<<EOD
                <table:table-column table:default-cell-style-name='Default' table:style-name="co{$styleIndex}" table:number-columns-repeated="{$numCols}"/>
                EOD;
        }
        \assert(isset($columnWidth));
        
        
        $content .= '<table:table-column table:default-cell-style-name="ce1" table:style-name="default-column-style" table:number-columns-repeated="'.($maxNumColumns - $columnWidth->end).'"/>';

        return $content;
    }

    
    private function getStylesSectionContent(): string
    {
        $defaultStyle = $this->getDefaultStyle();

        return <<<EOD
            <office:styles>
                <number:number-style style:name="N0">
                    <number:number number:min-integer-digits="1"/>
                </number:number-style>
                <style:style style:data-style-name="N0" style:family="table-cell" style:name="Default">
                    <style:table-cell-properties fo:background-color="transparent" style:vertical-align="automatic"/>
                    <style:text-properties fo:color="#{$defaultStyle->getFontColor()}"
                                           fo:font-size="{$defaultStyle->getFontSize()}pt" style:font-size-asian="{$defaultStyle->getFontSize()}pt" style:font-size-complex="{$defaultStyle->getFontSize()}pt"
                                           style:font-name="{$defaultStyle->getFontName()}" style:font-name-asian="{$defaultStyle->getFontName()}" style:font-name-complex="{$defaultStyle->getFontName()}"/>
                </style:style>
            </office:styles>
            EOD;
    }

    
    private function getMasterStylesSectionContent(int $numWorksheets): string
    {
        $content = '<office:master-styles>';

        for ($i = 1; $i <= $numWorksheets; ++$i) {
            $content .= <<<EOD
                <style:master-page style:name="mp{$i}" style:page-layout-name="pm{$i}">
                    <style:header/>
                    <style:header-left style:display="false"/>
                    <style:footer/>
                    <style:footer-left style:display="false"/>
                </style:master-page>
                EOD;
        }

        $content .= '</office:master-styles>';

        return $content;
    }

    
    private function getFontFaceSectionContent(): string
    {
        $content = '<office:font-face-decls>';
        foreach ($this->styleRegistry->getUsedFonts() as $fontName) {
            $content .= '<style:font-face style:name="'.$fontName.'" svg:font-family="'.$fontName.'"/>';
        }
        $content .= '</office:font-face-decls>';

        return $content;
    }

    
    private function getAutomaticStylesSectionContent(int $numWorksheets): string
    {
        $content = '<office:automatic-styles>';

        for ($i = 1; $i <= $numWorksheets; ++$i) {
            $content .= <<<EOD
                <style:page-layout style:name="pm{$i}">
                    <style:page-layout-properties style:first-page-number="continue" style:print="objects charts drawings" style:table-centering="none"/>
                    <style:header-style/>
                    <style:footer-style/>
                </style:page-layout>
                EOD;
        }

        $content .= '</office:automatic-styles>';

        return $content;
    }

    
    private function getStyleSectionContent(Style $style): string
    {
        $styleIndex = $style->getId() + 1; 

        $content = '<style:style style:data-style-name="N0" style:family="table-cell" style:name="ce'.$styleIndex.'" style:parent-style-name="Default">';

        $content .= $this->getTextPropertiesSectionContent($style);
        $content .= $this->getParagraphPropertiesSectionContent($style);
        $content .= $this->getTableCellPropertiesSectionContent($style);

        $content .= '</style:style>';

        return $content;
    }

    
    private function getTextPropertiesSectionContent(Style $style): string
    {
        if (!$style->shouldApplyFont()) {
            return '';
        }

        return '<style:text-properties '
            .$this->getFontSectionContent($style)
            .'/>';
    }

    
    private function getFontSectionContent(Style $style): string
    {
        $defaultStyle = $this->getDefaultStyle();
        $content = '';

        $fontColor = $style->getFontColor();
        if ($fontColor !== $defaultStyle->getFontColor()) {
            $content .= ' fo:color="#'.$fontColor.'"';
        }

        $fontName = $style->getFontName();
        if ($fontName !== $defaultStyle->getFontName()) {
            $content .= ' style:font-name="'.$fontName.'" style:font-name-asian="'.$fontName.'" style:font-name-complex="'.$fontName.'"';
        }

        $fontSize = $style->getFontSize();
        if ($fontSize !== $defaultStyle->getFontSize()) {
            $content .= ' fo:font-size="'.$fontSize.'pt" style:font-size-asian="'.$fontSize.'pt" style:font-size-complex="'.$fontSize.'pt"';
        }

        if ($style->isFontBold()) {
            $content .= ' fo:font-weight="bold" style:font-weight-asian="bold" style:font-weight-complex="bold"';
        }
        if ($style->isFontItalic()) {
            $content .= ' fo:font-style="italic" style:font-style-asian="italic" style:font-style-complex="italic"';
        }
        if ($style->isFontUnderline()) {
            $content .= ' style:text-underline-style="solid" style:text-underline-type="single"';
        }
        if ($style->isFontStrikethrough()) {
            $content .= ' style:text-line-through-style="solid"';
        }

        return $content;
    }

    
    private function getParagraphPropertiesSectionContent(Style $style): string
    {
        if (!$style->shouldApplyCellAlignment() && !$style->shouldApplyCellVerticalAlignment()) {
            return '';
        }

        return '<style:paragraph-properties '
            .$this->getCellAlignmentSectionContent($style)
            .$this->getCellVerticalAlignmentSectionContent($style)
            .'/>';
    }

    
    private function getCellAlignmentSectionContent(Style $style): string
    {
        if (!$style->hasSetCellAlignment()) {
            return '';
        }

        return sprintf(
            ' fo:text-align="%s" ',
            $this->transformCellAlignment($style->getCellAlignment())
        );
    }

    
    private function getCellVerticalAlignmentSectionContent(Style $style): string
    {
        if (!$style->hasSetCellVerticalAlignment()) {
            return '';
        }

        return sprintf(
            ' fo:vertical-align="%s" ',
            $this->transformCellVerticalAlignment($style->getCellVerticalAlignment())
        );
    }

    
    private function transformCellAlignment(string $cellAlignment): string
    {
        return match ($cellAlignment) {
            CellAlignment::LEFT => 'start',
            CellAlignment::RIGHT => 'end',
            default => $cellAlignment,
        };
    }

    
    private function transformCellVerticalAlignment(string $cellVerticalAlignment): string
    {
        return (CellVerticalAlignment::CENTER === $cellVerticalAlignment)
            ? 'middle'
            : $cellVerticalAlignment;
    }

    
    private function getTableCellPropertiesSectionContent(Style $style): string
    {
        $content = '<style:table-cell-properties ';

        if ($style->hasSetWrapText()) {
            $content .= $this->getWrapTextXMLContent($style->shouldWrapText());
        }

        if (null !== ($border = $style->getBorder())) {
            $content .= $this->getBorderXMLContent($border);
        }

        if (null !== ($bgColor = $style->getBackgroundColor())) {
            $content .= $this->getBackgroundColorXMLContent($bgColor);
        }

        $content .= '/>';

        return $content;
    }

    
    private function getWrapTextXMLContent(bool $shouldWrapText): string
    {
        return ' fo:wrap-option="'.($shouldWrapText ? '' : 'no-').'wrap" style:vertical-align="automatic" ';
    }

    
    private function getBorderXMLContent(Border $border): string
    {
        $borders = array_map(static function (BorderPart $borderPart) {
            return BorderHelper::serializeBorderPart($borderPart);
        }, $border->getParts());

        return sprintf(' %s ', implode(' ', $borders));
    }

    
    private function getBackgroundColorXMLContent(string $bgColor): string
    {
        return sprintf(' fo:background-color="#%s" ', $bgColor);
    }
}
