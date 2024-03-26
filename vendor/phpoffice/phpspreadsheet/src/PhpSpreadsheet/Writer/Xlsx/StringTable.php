<?php

namespace PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\RichText\Run;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;
use PhpOffice\PhpSpreadsheet\Shared\XMLWriter;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StringTable extends WriterPart
{
    
    public function createStringTable(Worksheet $pSheet, $pExistingTable = null)
    {
        
        $aStringTable = [];
        $cellCollection = null;
        $aFlippedStringTable = null; 

        
        if (($pExistingTable !== null) && is_array($pExistingTable)) {
            $aStringTable = $pExistingTable;
        }

        
        $aFlippedStringTable = $this->flipStringTable($aStringTable);

        
        foreach ($pSheet->getCoordinates() as $coordinate) {
            $cell = $pSheet->getCell($coordinate);
            $cellValue = $cell->getValue();
            if (
                !is_object($cellValue) &&
                ($cellValue !== null) &&
                $cellValue !== '' &&
                !isset($aFlippedStringTable[$cellValue]) &&
                ($cell->getDataType() == DataType::TYPE_STRING || $cell->getDataType() == DataType::TYPE_STRING2 || $cell->getDataType() == DataType::TYPE_NULL)
            ) {
                $aStringTable[] = $cellValue;
                $aFlippedStringTable[$cellValue] = true;
            } elseif (
                $cellValue instanceof RichText &&
                ($cellValue !== null) &&
                !isset($aFlippedStringTable[$cellValue->getHashCode()])
            ) {
                $aStringTable[] = $cellValue;
                $aFlippedStringTable[$cellValue->getHashCode()] = true;
            }
        }

        return $aStringTable;
    }

    
    public function writeStringTable(array $pStringTable)
    {
        
        $objWriter = null;
        if ($this->getParentWriter()->getUseDiskCaching()) {
            $objWriter = new XMLWriter(XMLWriter::STORAGE_DISK, $this->getParentWriter()->getDiskCachingDirectory());
        } else {
            $objWriter = new XMLWriter(XMLWriter::STORAGE_MEMORY);
        }

        
        $objWriter->startDocument('1.0', 'UTF-8', 'yes');

        
        $objWriter->startElement('sst');
        $objWriter->writeAttribute('xmlns', 'http:
        $objWriter->writeAttribute('uniqueCount', count($pStringTable));

        
        foreach ($pStringTable as $textElement) {
            $objWriter->startElement('si');

            if (!$textElement instanceof RichText) {
                $textToWrite = StringHelper::controlCharacterPHP2OOXML($textElement);
                $objWriter->startElement('t');
                if ($textToWrite !== trim($textToWrite)) {
                    $objWriter->writeAttribute('xml:space', 'preserve');
                }
                $objWriter->writeRawData($textToWrite);
                $objWriter->endElement();
            } elseif ($textElement instanceof RichText) {
                $this->writeRichText($objWriter, $textElement);
            }

            $objWriter->endElement();
        }

        $objWriter->endElement();

        return $objWriter->getData();
    }

    
    public function writeRichText(XMLWriter $objWriter, RichText $pRichText, $prefix = null): void
    {
        if ($prefix !== null) {
            $prefix .= ':';
        }

        
        $elements = $pRichText->getRichTextElements();
        foreach ($elements as $element) {
            
            $objWriter->startElement($prefix . 'r');

            
            if ($element instanceof Run) {
                
                $objWriter->startElement($prefix . 'rPr');

                
                $objWriter->startElement($prefix . 'rFont');
                $objWriter->writeAttribute('val', $element->getFont()->getName());
                $objWriter->endElement();

                
                $objWriter->startElement($prefix . 'b');
                $objWriter->writeAttribute('val', ($element->getFont()->getBold() ? 'true' : 'false'));
                $objWriter->endElement();

                
                $objWriter->startElement($prefix . 'i');
                $objWriter->writeAttribute('val', ($element->getFont()->getItalic() ? 'true' : 'false'));
                $objWriter->endElement();

                
                if ($element->getFont()->getSuperscript() || $element->getFont()->getSubscript()) {
                    $objWriter->startElement($prefix . 'vertAlign');
                    if ($element->getFont()->getSuperscript()) {
                        $objWriter->writeAttribute('val', 'superscript');
                    } elseif ($element->getFont()->getSubscript()) {
                        $objWriter->writeAttribute('val', 'subscript');
                    }
                    $objWriter->endElement();
                }

                
                $objWriter->startElement($prefix . 'strike');
                $objWriter->writeAttribute('val', ($element->getFont()->getStrikethrough() ? 'true' : 'false'));
                $objWriter->endElement();

                
                $objWriter->startElement($prefix . 'color');
                $objWriter->writeAttribute('rgb', $element->getFont()->getColor()->getARGB());
                $objWriter->endElement();

                
                $objWriter->startElement($prefix . 'sz');
                $objWriter->writeAttribute('val', $element->getFont()->getSize());
                $objWriter->endElement();

                
                $objWriter->startElement($prefix . 'u');
                $objWriter->writeAttribute('val', $element->getFont()->getUnderline());
                $objWriter->endElement();

                $objWriter->endElement();
            }

            
            $objWriter->startElement($prefix . 't');
            $objWriter->writeAttribute('xml:space', 'preserve');
            $objWriter->writeRawData(StringHelper::controlCharacterPHP2OOXML($element->getText()));
            $objWriter->endElement();

            $objWriter->endElement();
        }
    }

    
    public function writeRichTextForCharts(XMLWriter $objWriter, $pRichText = null, $prefix = null): void
    {
        if (!$pRichText instanceof RichText) {
            $textRun = $pRichText;
            $pRichText = new RichText();
            $pRichText->createTextRun($textRun);
        }

        if ($prefix !== null) {
            $prefix .= ':';
        }

        
        $elements = $pRichText->getRichTextElements();
        foreach ($elements as $element) {
            
            $objWriter->startElement($prefix . 'r');

            
            $objWriter->startElement($prefix . 'rPr');

            
            $objWriter->writeAttribute('b', ($element->getFont()->getBold() ? 1 : 0));
            
            $objWriter->writeAttribute('i', ($element->getFont()->getItalic() ? 1 : 0));
            
            $underlineType = $element->getFont()->getUnderline();
            switch ($underlineType) {
                case 'single':
                    $underlineType = 'sng';

                    break;
                case 'double':
                    $underlineType = 'dbl';

                    break;
            }
            $objWriter->writeAttribute('u', $underlineType);
            
            $objWriter->writeAttribute('strike', ($element->getFont()->getStrikethrough() ? 'sngStrike' : 'noStrike'));

            
            $objWriter->startElement($prefix . 'latin');
            $objWriter->writeAttribute('typeface', $element->getFont()->getName());
            $objWriter->endElement();

            $objWriter->endElement();

            
            $objWriter->startElement($prefix . 't');
            $objWriter->writeRawData(StringHelper::controlCharacterPHP2OOXML($element->getText()));
            $objWriter->endElement();

            $objWriter->endElement();
        }
    }

    
    public function flipStringTable(array $stringTable)
    {
        
        $returnValue = [];

        
        foreach ($stringTable as $key => $value) {
            if (!$value instanceof RichText) {
                $returnValue[$value] = $key;
            } elseif ($value instanceof RichText) {
                $returnValue[$value->getHashCode()] = $key;
            }
        }

        return $returnValue;
    }
}
