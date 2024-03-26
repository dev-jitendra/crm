<?php

namespace PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Shared\StringHelper;
use PhpOffice\PhpSpreadsheet\Shared\XMLWriter;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Borders;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Protection;

class Style extends WriterPart
{
    
    public function writeStyles(Spreadsheet $spreadsheet)
    {
        
        $objWriter = null;
        if ($this->getParentWriter()->getUseDiskCaching()) {
            $objWriter = new XMLWriter(XMLWriter::STORAGE_DISK, $this->getParentWriter()->getDiskCachingDirectory());
        } else {
            $objWriter = new XMLWriter(XMLWriter::STORAGE_MEMORY);
        }

        
        $objWriter->startDocument('1.0', 'UTF-8', 'yes');

        
        $objWriter->startElement('styleSheet');
        $objWriter->writeAttribute('xml:space', 'preserve');
        $objWriter->writeAttribute('xmlns', 'http:

        
        $objWriter->startElement('numFmts');
        $objWriter->writeAttribute('count', $this->getParentWriter()->getNumFmtHashTable()->count());

        
        for ($i = 0; $i < $this->getParentWriter()->getNumFmtHashTable()->count(); ++$i) {
            $this->writeNumFmt($objWriter, $this->getParentWriter()->getNumFmtHashTable()->getByIndex($i), $i);
        }

        $objWriter->endElement();

        
        $objWriter->startElement('fonts');
        $objWriter->writeAttribute('count', $this->getParentWriter()->getFontHashTable()->count());

        
        for ($i = 0; $i < $this->getParentWriter()->getFontHashTable()->count(); ++$i) {
            $this->writeFont($objWriter, $this->getParentWriter()->getFontHashTable()->getByIndex($i));
        }

        $objWriter->endElement();

        
        $objWriter->startElement('fills');
        $objWriter->writeAttribute('count', $this->getParentWriter()->getFillHashTable()->count());

        
        for ($i = 0; $i < $this->getParentWriter()->getFillHashTable()->count(); ++$i) {
            $this->writeFill($objWriter, $this->getParentWriter()->getFillHashTable()->getByIndex($i));
        }

        $objWriter->endElement();

        
        $objWriter->startElement('borders');
        $objWriter->writeAttribute('count', $this->getParentWriter()->getBordersHashTable()->count());

        
        for ($i = 0; $i < $this->getParentWriter()->getBordersHashTable()->count(); ++$i) {
            $this->writeBorder($objWriter, $this->getParentWriter()->getBordersHashTable()->getByIndex($i));
        }

        $objWriter->endElement();

        
        $objWriter->startElement('cellStyleXfs');
        $objWriter->writeAttribute('count', 1);

        
        $objWriter->startElement('xf');
        $objWriter->writeAttribute('numFmtId', 0);
        $objWriter->writeAttribute('fontId', 0);
        $objWriter->writeAttribute('fillId', 0);
        $objWriter->writeAttribute('borderId', 0);
        $objWriter->endElement();

        $objWriter->endElement();

        
        $objWriter->startElement('cellXfs');
        $objWriter->writeAttribute('count', count($spreadsheet->getCellXfCollection()));

        
        foreach ($spreadsheet->getCellXfCollection() as $cellXf) {
            $this->writeCellStyleXf($objWriter, $cellXf, $spreadsheet);
        }

        $objWriter->endElement();

        
        $objWriter->startElement('cellStyles');
        $objWriter->writeAttribute('count', 1);

        
        $objWriter->startElement('cellStyle');
        $objWriter->writeAttribute('name', 'Normal');
        $objWriter->writeAttribute('xfId', 0);
        $objWriter->writeAttribute('builtinId', 0);
        $objWriter->endElement();

        $objWriter->endElement();

        
        $objWriter->startElement('dxfs');
        $objWriter->writeAttribute('count', $this->getParentWriter()->getStylesConditionalHashTable()->count());

        
        for ($i = 0; $i < $this->getParentWriter()->getStylesConditionalHashTable()->count(); ++$i) {
            $this->writeCellStyleDxf($objWriter, $this->getParentWriter()->getStylesConditionalHashTable()->getByIndex($i)->getStyle());
        }

        $objWriter->endElement();

        
        $objWriter->startElement('tableStyles');
        $objWriter->writeAttribute('defaultTableStyle', 'TableStyleMedium9');
        $objWriter->writeAttribute('defaultPivotStyle', 'PivotTableStyle1');
        $objWriter->endElement();

        $objWriter->endElement();

        
        return $objWriter->getData();
    }

    
    private function writeFill(XMLWriter $objWriter, Fill $pFill): void
    {
        
        if (
            $pFill->getFillType() === Fill::FILL_GRADIENT_LINEAR ||
            $pFill->getFillType() === Fill::FILL_GRADIENT_PATH
        ) {
            
            $this->writeGradientFill($objWriter, $pFill);
        } elseif ($pFill->getFillType() !== null) {
            
            $this->writePatternFill($objWriter, $pFill);
        }
    }

    
    private function writeGradientFill(XMLWriter $objWriter, Fill $pFill): void
    {
        
        $objWriter->startElement('fill');

        
        $objWriter->startElement('gradientFill');
        $objWriter->writeAttribute('type', $pFill->getFillType());
        $objWriter->writeAttribute('degree', $pFill->getRotation());

        
        $objWriter->startElement('stop');
        $objWriter->writeAttribute('position', '0');

        
        $objWriter->startElement('color');
        $objWriter->writeAttribute('rgb', $pFill->getStartColor()->getARGB());
        $objWriter->endElement();

        $objWriter->endElement();

        
        $objWriter->startElement('stop');
        $objWriter->writeAttribute('position', '1');

        
        $objWriter->startElement('color');
        $objWriter->writeAttribute('rgb', $pFill->getEndColor()->getARGB());
        $objWriter->endElement();

        $objWriter->endElement();

        $objWriter->endElement();

        $objWriter->endElement();
    }

    
    private function writePatternFill(XMLWriter $objWriter, Fill $pFill): void
    {
        
        $objWriter->startElement('fill');

        
        $objWriter->startElement('patternFill');
        $objWriter->writeAttribute('patternType', $pFill->getFillType());

        if ($pFill->getFillType() !== Fill::FILL_NONE) {
            
            if ($pFill->getStartColor()->getARGB()) {
                $objWriter->startElement('fgColor');
                $objWriter->writeAttribute('rgb', $pFill->getStartColor()->getARGB());
                $objWriter->endElement();
            }
        }
        if ($pFill->getFillType() !== Fill::FILL_NONE) {
            
            if ($pFill->getEndColor()->getARGB()) {
                $objWriter->startElement('bgColor');
                $objWriter->writeAttribute('rgb', $pFill->getEndColor()->getARGB());
                $objWriter->endElement();
            }
        }

        $objWriter->endElement();

        $objWriter->endElement();
    }

    
    private function writeFont(XMLWriter $objWriter, Font $pFont): void
    {
        
        $objWriter->startElement('font');
        
        
        

        
        
        
        if ($pFont->getBold() !== null) {
            $objWriter->startElement('b');
            $objWriter->writeAttribute('val', $pFont->getBold() ? '1' : '0');
            $objWriter->endElement();
        }

        
        if ($pFont->getItalic() !== null) {
            $objWriter->startElement('i');
            $objWriter->writeAttribute('val', $pFont->getItalic() ? '1' : '0');
            $objWriter->endElement();
        }

        
        if ($pFont->getStrikethrough() !== null) {
            $objWriter->startElement('strike');
            $objWriter->writeAttribute('val', $pFont->getStrikethrough() ? '1' : '0');
            $objWriter->endElement();
        }

        
        if ($pFont->getUnderline() !== null) {
            $objWriter->startElement('u');
            $objWriter->writeAttribute('val', $pFont->getUnderline());
            $objWriter->endElement();
        }

        
        if ($pFont->getSuperscript() === true || $pFont->getSubscript() === true) {
            $objWriter->startElement('vertAlign');
            if ($pFont->getSuperscript() === true) {
                $objWriter->writeAttribute('val', 'superscript');
            } elseif ($pFont->getSubscript() === true) {
                $objWriter->writeAttribute('val', 'subscript');
            }
            $objWriter->endElement();
        }

        
        if ($pFont->getSize() !== null) {
            $objWriter->startElement('sz');
            $objWriter->writeAttribute('val', StringHelper::formatNumber($pFont->getSize()));
            $objWriter->endElement();
        }

        
        if ($pFont->getColor()->getARGB() !== null) {
            $objWriter->startElement('color');
            $objWriter->writeAttribute('rgb', $pFont->getColor()->getARGB());
            $objWriter->endElement();
        }

        
        if ($pFont->getName() !== null) {
            $objWriter->startElement('name');
            $objWriter->writeAttribute('val', $pFont->getName());
            $objWriter->endElement();
        }

        $objWriter->endElement();
    }

    
    private function writeBorder(XMLWriter $objWriter, Borders $pBorders): void
    {
        
        $objWriter->startElement('border');
        
        switch ($pBorders->getDiagonalDirection()) {
            case Borders::DIAGONAL_UP:
                $objWriter->writeAttribute('diagonalUp', 'true');
                $objWriter->writeAttribute('diagonalDown', 'false');

                break;
            case Borders::DIAGONAL_DOWN:
                $objWriter->writeAttribute('diagonalUp', 'false');
                $objWriter->writeAttribute('diagonalDown', 'true');

                break;
            case Borders::DIAGONAL_BOTH:
                $objWriter->writeAttribute('diagonalUp', 'true');
                $objWriter->writeAttribute('diagonalDown', 'true');

                break;
        }

        
        $this->writeBorderPr($objWriter, 'left', $pBorders->getLeft());
        $this->writeBorderPr($objWriter, 'right', $pBorders->getRight());
        $this->writeBorderPr($objWriter, 'top', $pBorders->getTop());
        $this->writeBorderPr($objWriter, 'bottom', $pBorders->getBottom());
        $this->writeBorderPr($objWriter, 'diagonal', $pBorders->getDiagonal());
        $objWriter->endElement();
    }

    
    private function writeCellStyleXf(XMLWriter $objWriter, \PhpOffice\PhpSpreadsheet\Style\Style $pStyle, Spreadsheet $spreadsheet): void
    {
        
        $objWriter->startElement('xf');
        $objWriter->writeAttribute('xfId', 0);
        $objWriter->writeAttribute('fontId', (int) $this->getParentWriter()->getFontHashTable()->getIndexForHashCode($pStyle->getFont()->getHashCode()));
        if ($pStyle->getQuotePrefix()) {
            $objWriter->writeAttribute('quotePrefix', 1);
        }

        if ($pStyle->getNumberFormat()->getBuiltInFormatCode() === false) {
            $objWriter->writeAttribute('numFmtId', (int) ($this->getParentWriter()->getNumFmtHashTable()->getIndexForHashCode($pStyle->getNumberFormat()->getHashCode()) + 164));
        } else {
            $objWriter->writeAttribute('numFmtId', (int) $pStyle->getNumberFormat()->getBuiltInFormatCode());
        }

        $objWriter->writeAttribute('fillId', (int) $this->getParentWriter()->getFillHashTable()->getIndexForHashCode($pStyle->getFill()->getHashCode()));
        $objWriter->writeAttribute('borderId', (int) $this->getParentWriter()->getBordersHashTable()->getIndexForHashCode($pStyle->getBorders()->getHashCode()));

        
        $objWriter->writeAttribute('applyFont', ($spreadsheet->getDefaultStyle()->getFont()->getHashCode() != $pStyle->getFont()->getHashCode()) ? '1' : '0');
        $objWriter->writeAttribute('applyNumberFormat', ($spreadsheet->getDefaultStyle()->getNumberFormat()->getHashCode() != $pStyle->getNumberFormat()->getHashCode()) ? '1' : '0');
        $objWriter->writeAttribute('applyFill', ($spreadsheet->getDefaultStyle()->getFill()->getHashCode() != $pStyle->getFill()->getHashCode()) ? '1' : '0');
        $objWriter->writeAttribute('applyBorder', ($spreadsheet->getDefaultStyle()->getBorders()->getHashCode() != $pStyle->getBorders()->getHashCode()) ? '1' : '0');
        $objWriter->writeAttribute('applyAlignment', ($spreadsheet->getDefaultStyle()->getAlignment()->getHashCode() != $pStyle->getAlignment()->getHashCode()) ? '1' : '0');
        if ($pStyle->getProtection()->getLocked() != Protection::PROTECTION_INHERIT || $pStyle->getProtection()->getHidden() != Protection::PROTECTION_INHERIT) {
            $objWriter->writeAttribute('applyProtection', 'true');
        }

        
        $objWriter->startElement('alignment');
        $objWriter->writeAttribute('horizontal', $pStyle->getAlignment()->getHorizontal());
        $objWriter->writeAttribute('vertical', $pStyle->getAlignment()->getVertical());

        $textRotation = 0;
        if ($pStyle->getAlignment()->getTextRotation() >= 0) {
            $textRotation = $pStyle->getAlignment()->getTextRotation();
        } elseif ($pStyle->getAlignment()->getTextRotation() < 0) {
            $textRotation = 90 - $pStyle->getAlignment()->getTextRotation();
        }
        $objWriter->writeAttribute('textRotation', $textRotation);

        $objWriter->writeAttribute('wrapText', ($pStyle->getAlignment()->getWrapText() ? 'true' : 'false'));
        $objWriter->writeAttribute('shrinkToFit', ($pStyle->getAlignment()->getShrinkToFit() ? 'true' : 'false'));

        if ($pStyle->getAlignment()->getIndent() > 0) {
            $objWriter->writeAttribute('indent', $pStyle->getAlignment()->getIndent());
        }
        if ($pStyle->getAlignment()->getReadOrder() > 0) {
            $objWriter->writeAttribute('readingOrder', $pStyle->getAlignment()->getReadOrder());
        }
        $objWriter->endElement();

        
        if ($pStyle->getProtection()->getLocked() != Protection::PROTECTION_INHERIT || $pStyle->getProtection()->getHidden() != Protection::PROTECTION_INHERIT) {
            $objWriter->startElement('protection');
            if ($pStyle->getProtection()->getLocked() != Protection::PROTECTION_INHERIT) {
                $objWriter->writeAttribute('locked', ($pStyle->getProtection()->getLocked() == Protection::PROTECTION_PROTECTED ? 'true' : 'false'));
            }
            if ($pStyle->getProtection()->getHidden() != Protection::PROTECTION_INHERIT) {
                $objWriter->writeAttribute('hidden', ($pStyle->getProtection()->getHidden() == Protection::PROTECTION_PROTECTED ? 'true' : 'false'));
            }
            $objWriter->endElement();
        }

        $objWriter->endElement();
    }

    
    private function writeCellStyleDxf(XMLWriter $objWriter, \PhpOffice\PhpSpreadsheet\Style\Style $pStyle): void
    {
        
        $objWriter->startElement('dxf');

        
        $this->writeFont($objWriter, $pStyle->getFont());

        
        $this->writeNumFmt($objWriter, $pStyle->getNumberFormat());

        
        $this->writeFill($objWriter, $pStyle->getFill());

        
        $objWriter->startElement('alignment');
        if ($pStyle->getAlignment()->getHorizontal() !== null) {
            $objWriter->writeAttribute('horizontal', $pStyle->getAlignment()->getHorizontal());
        }
        if ($pStyle->getAlignment()->getVertical() !== null) {
            $objWriter->writeAttribute('vertical', $pStyle->getAlignment()->getVertical());
        }

        if ($pStyle->getAlignment()->getTextRotation() !== null) {
            $textRotation = 0;
            if ($pStyle->getAlignment()->getTextRotation() >= 0) {
                $textRotation = $pStyle->getAlignment()->getTextRotation();
            } elseif ($pStyle->getAlignment()->getTextRotation() < 0) {
                $textRotation = 90 - $pStyle->getAlignment()->getTextRotation();
            }
            $objWriter->writeAttribute('textRotation', $textRotation);
        }
        $objWriter->endElement();

        
        $this->writeBorder($objWriter, $pStyle->getBorders());

        
        if (($pStyle->getProtection()->getLocked() !== null) || ($pStyle->getProtection()->getHidden() !== null)) {
            if (
                $pStyle->getProtection()->getLocked() !== Protection::PROTECTION_INHERIT ||
                $pStyle->getProtection()->getHidden() !== Protection::PROTECTION_INHERIT
            ) {
                $objWriter->startElement('protection');
                if (
                    ($pStyle->getProtection()->getLocked() !== null) &&
                    ($pStyle->getProtection()->getLocked() !== Protection::PROTECTION_INHERIT)
                ) {
                    $objWriter->writeAttribute('locked', ($pStyle->getProtection()->getLocked() == Protection::PROTECTION_PROTECTED ? 'true' : 'false'));
                }
                if (
                    ($pStyle->getProtection()->getHidden() !== null) &&
                    ($pStyle->getProtection()->getHidden() !== Protection::PROTECTION_INHERIT)
                ) {
                    $objWriter->writeAttribute('hidden', ($pStyle->getProtection()->getHidden() == Protection::PROTECTION_PROTECTED ? 'true' : 'false'));
                }
                $objWriter->endElement();
            }
        }

        $objWriter->endElement();
    }

    
    private function writeBorderPr(XMLWriter $objWriter, $pName, Border $pBorder): void
    {
        
        if ($pBorder->getBorderStyle() != Border::BORDER_NONE) {
            $objWriter->startElement($pName);
            $objWriter->writeAttribute('style', $pBorder->getBorderStyle());

            
            $objWriter->startElement('color');
            $objWriter->writeAttribute('rgb', $pBorder->getColor()->getARGB());
            $objWriter->endElement();

            $objWriter->endElement();
        }
    }

    
    private function writeNumFmt(XMLWriter $objWriter, NumberFormat $pNumberFormat, $pId = 0): void
    {
        
        $formatCode = $pNumberFormat->getFormatCode();

        
        if ($formatCode !== null) {
            $objWriter->startElement('numFmt');
            $objWriter->writeAttribute('numFmtId', ($pId + 164));
            $objWriter->writeAttribute('formatCode', $formatCode);
            $objWriter->endElement();
        }
    }

    
    public function allStyles(Spreadsheet $spreadsheet)
    {
        return $spreadsheet->getCellXfCollection();
    }

    
    public function allConditionalStyles(Spreadsheet $spreadsheet)
    {
        
        $aStyles = [];

        $sheetCount = $spreadsheet->getSheetCount();
        for ($i = 0; $i < $sheetCount; ++$i) {
            foreach ($spreadsheet->getSheet($i)->getConditionalStylesCollection() as $conditionalStyles) {
                foreach ($conditionalStyles as $conditionalStyle) {
                    $aStyles[] = $conditionalStyle;
                }
            }
        }

        return $aStyles;
    }

    
    public function allFills(Spreadsheet $spreadsheet)
    {
        
        $aFills = [];

        
        $fill0 = new Fill();
        $fill0->setFillType(Fill::FILL_NONE);
        $aFills[] = $fill0;

        $fill1 = new Fill();
        $fill1->setFillType(Fill::FILL_PATTERN_GRAY125);
        $aFills[] = $fill1;
        
        $aStyles = $this->allStyles($spreadsheet);
        
        foreach ($aStyles as $style) {
            if (!isset($aFills[$style->getFill()->getHashCode()])) {
                $aFills[$style->getFill()->getHashCode()] = $style->getFill();
            }
        }

        return $aFills;
    }

    
    public function allFonts(Spreadsheet $spreadsheet)
    {
        
        $aFonts = [];
        $aStyles = $this->allStyles($spreadsheet);

        
        foreach ($aStyles as $style) {
            if (!isset($aFonts[$style->getFont()->getHashCode()])) {
                $aFonts[$style->getFont()->getHashCode()] = $style->getFont();
            }
        }

        return $aFonts;
    }

    
    public function allBorders(Spreadsheet $spreadsheet)
    {
        
        $aBorders = [];
        $aStyles = $this->allStyles($spreadsheet);

        
        foreach ($aStyles as $style) {
            if (!isset($aBorders[$style->getBorders()->getHashCode()])) {
                $aBorders[$style->getBorders()->getHashCode()] = $style->getBorders();
            }
        }

        return $aBorders;
    }

    
    public function allNumberFormats(Spreadsheet $spreadsheet)
    {
        
        $aNumFmts = [];
        $aStyles = $this->allStyles($spreadsheet);

        
        foreach ($aStyles as $style) {
            if ($style->getNumberFormat()->getBuiltInFormatCode() === false && !isset($aNumFmts[$style->getNumberFormat()->getHashCode()])) {
                $aNumFmts[$style->getNumberFormat()->getHashCode()] = $style->getNumberFormat();
            }
        }

        return $aNumFmts;
    }
}
