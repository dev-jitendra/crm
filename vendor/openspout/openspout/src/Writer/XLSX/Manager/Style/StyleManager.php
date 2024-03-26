<?php

declare(strict_types=1);

namespace OpenSpout\Writer\XLSX\Manager\Style;

use OpenSpout\Common\Entity\Style\BorderPart;
use OpenSpout\Common\Entity\Style\Color;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\Common\Manager\Style\AbstractStyleManager as CommonStyleManager;
use OpenSpout\Writer\XLSX\Helper\BorderHelper;


final class StyleManager extends CommonStyleManager
{
    public function __construct(StyleRegistry $styleRegistry)
    {
        parent::__construct($styleRegistry);
    }

    
    public function shouldApplyStyleOnEmptyCell(?int $styleId): bool
    {
        if (null === $styleId) {
            return false;
        }
        $associatedFillId = $this->styleRegistry->getFillIdForStyleId($styleId);
        $hasStyleCustomFill = (null !== $associatedFillId && 0 !== $associatedFillId);

        $associatedBorderId = $this->styleRegistry->getBorderIdForStyleId($styleId);
        $hasStyleCustomBorders = (null !== $associatedBorderId && 0 !== $associatedBorderId);

        $associatedFormatId = $this->styleRegistry->getFormatIdForStyleId($styleId);
        $hasStyleCustomFormats = (null !== $associatedFormatId && 0 !== $associatedFormatId);

        return $hasStyleCustomFill || $hasStyleCustomBorders || $hasStyleCustomFormats;
    }

    
    public function getStylesXMLFileContent(): string
    {
        $content = <<<'EOD'
            <?xml version="1.0" encoding="UTF-8" standalone="yes"?>
            <styleSheet xmlns="http:
            EOD;

        $content .= $this->getFormatsSectionContent();
        $content .= $this->getFontsSectionContent();
        $content .= $this->getFillsSectionContent();
        $content .= $this->getBordersSectionContent();
        $content .= $this->getCellStyleXfsSectionContent();
        $content .= $this->getCellXfsSectionContent();
        $content .= $this->getCellStylesSectionContent();

        $content .= <<<'EOD'
            </styleSheet>
            EOD;

        return $content;
    }

    
    private function getFormatsSectionContent(): string
    {
        $tags = [];
        $registeredFormats = $this->styleRegistry->getRegisteredFormats();
        foreach ($registeredFormats as $styleId) {
            $numFmtId = $this->styleRegistry->getFormatIdForStyleId($styleId);

            
            if ($numFmtId < 164) {
                continue;
            }

            
            $style = $this->styleRegistry->getStyleFromStyleId($styleId);
            $format = $style->getFormat();
            $tags[] = '<numFmt numFmtId="'.$numFmtId.'" formatCode="'.$format.'"/>';
        }
        $content = '<numFmts count="'.\count($tags).'">';
        $content .= implode('', $tags);
        $content .= '</numFmts>';

        return $content;
    }

    
    private function getFontsSectionContent(): string
    {
        $registeredStyles = $this->styleRegistry->getRegisteredStyles();

        $content = '<fonts count="'.\count($registeredStyles).'">';

        
        foreach ($registeredStyles as $style) {
            $content .= '<font>';

            $content .= '<sz val="'.$style->getFontSize().'"/>';
            $content .= '<color rgb="'.Color::toARGB($style->getFontColor()).'"/>';
            $content .= '<name val="'.$style->getFontName().'"/>';

            if ($style->isFontBold()) {
                $content .= '<b/>';
            }
            if ($style->isFontItalic()) {
                $content .= '<i/>';
            }
            if ($style->isFontUnderline()) {
                $content .= '<u/>';
            }
            if ($style->isFontStrikethrough()) {
                $content .= '<strike/>';
            }

            $content .= '</font>';
        }

        $content .= '</fonts>';

        return $content;
    }

    
    private function getFillsSectionContent(): string
    {
        $registeredFills = $this->styleRegistry->getRegisteredFills();

        
        $fillsCount = \count($registeredFills) + 2;
        $content = sprintf('<fills count="%d">', $fillsCount);

        $content .= '<fill><patternFill patternType="none"/></fill>';
        $content .= '<fill><patternFill patternType="gray125"/></fill>';

        
        foreach ($registeredFills as $styleId) {
            
            $style = $this->styleRegistry->getStyleFromStyleId($styleId);

            $backgroundColor = $style->getBackgroundColor();
            $content .= sprintf(
                '<fill><patternFill patternType="solid"><fgColor rgb="%s"/></patternFill></fill>',
                $backgroundColor
            );
        }

        $content .= '</fills>';

        return $content;
    }

    
    private function getBordersSectionContent(): string
    {
        $registeredBorders = $this->styleRegistry->getRegisteredBorders();

        
        $borderCount = \count($registeredBorders) + 1;

        $content = '<borders count="'.$borderCount.'">';

        
        $content .= '<border><left/><right/><top/><bottom/></border>';

        foreach ($registeredBorders as $styleId) {
            $style = $this->styleRegistry->getStyleFromStyleId($styleId);
            $border = $style->getBorder();
            \assert(null !== $border);
            $content .= '<border>';

            
            foreach (BorderPart::allowedNames as $partName) {
                $content .= BorderHelper::serializeBorderPart($border->getPart($partName));
            }

            $content .= '</border>';
        }

        $content .= '</borders>';

        return $content;
    }

    
    private function getCellStyleXfsSectionContent(): string
    {
        return <<<'EOD'
            <cellStyleXfs count="1">
                <xf borderId="0" fillId="0" fontId="0" numFmtId="0"/>
            </cellStyleXfs>
            EOD;
    }

    
    private function getCellXfsSectionContent(): string
    {
        $registeredStyles = $this->styleRegistry->getRegisteredStyles();

        $content = '<cellXfs count="'.\count($registeredStyles).'">';

        foreach ($registeredStyles as $style) {
            $styleId = $style->getId();
            $fillId = $this->getFillIdForStyleId($styleId);
            $borderId = $this->getBorderIdForStyleId($styleId);
            $numFmtId = $this->getFormatIdForStyleId($styleId);

            $content .= '<xf numFmtId="'.$numFmtId.'" fontId="'.$styleId.'" fillId="'.$fillId.'" borderId="'.$borderId.'" xfId="0"';

            if ($style->shouldApplyFont()) {
                $content .= ' applyFont="1"';
            }

            $content .= sprintf(' applyBorder="%d"', (bool) $style->getBorder());

            if ($style->shouldApplyCellAlignment() || $style->shouldApplyCellVerticalAlignment() || $style->hasSetWrapText() || $style->shouldShrinkToFit()) {
                $content .= ' applyAlignment="1">';
                $content .= '<alignment';
                if ($style->shouldApplyCellAlignment()) {
                    $content .= sprintf(' horizontal="%s"', $style->getCellAlignment());
                }
                if ($style->shouldApplyCellVerticalAlignment()) {
                    $content .= sprintf(' vertical="%s"', $style->getCellVerticalAlignment());
                }
                if ($style->hasSetWrapText()) {
                    $content .= ' wrapText="'.($style->shouldWrapText() ? '1' : '0').'"';
                }
                if ($style->shouldShrinkToFit()) {
                    $content .= ' shrinkToFit="true"';
                }

                $content .= '/>';
                $content .= '</xf>';
            } else {
                $content .= '/>';
            }
        }

        $content .= '</cellXfs>';

        return $content;
    }

    
    private function getCellStylesSectionContent(): string
    {
        return <<<'EOD'
            <cellStyles count="1">
                <cellStyle builtinId="0" name="Normal" xfId="0"/>
            </cellStyles>
            EOD;
    }

    
    private function getFillIdForStyleId(int $styleId): int
    {
        
        
        $isDefaultStyle = (0 === $styleId);

        return $isDefaultStyle ? 0 : ($this->styleRegistry->getFillIdForStyleId($styleId) ?? 0);
    }

    
    private function getBorderIdForStyleId(int $styleId): int
    {
        
        
        $isDefaultStyle = (0 === $styleId);

        return $isDefaultStyle ? 0 : ($this->styleRegistry->getBorderIdForStyleId($styleId) ?? 0);
    }

    
    private function getFormatIdForStyleId(int $styleId): int
    {
        
        
        $isDefaultStyle = (0 === $styleId);

        return $isDefaultStyle ? 0 : ($this->styleRegistry->getFormatIdForStyleId($styleId) ?? 0);
    }
}
