<?php

namespace PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Shared\XMLWriter;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class DocProps extends WriterPart
{
    
    public function writeDocPropsApp(Spreadsheet $spreadsheet)
    {
        
        $objWriter = null;
        if ($this->getParentWriter()->getUseDiskCaching()) {
            $objWriter = new XMLWriter(XMLWriter::STORAGE_DISK, $this->getParentWriter()->getDiskCachingDirectory());
        } else {
            $objWriter = new XMLWriter(XMLWriter::STORAGE_MEMORY);
        }

        
        $objWriter->startDocument('1.0', 'UTF-8', 'yes');

        
        $objWriter->startElement('Properties');
        $objWriter->writeAttribute('xmlns', 'http:
        $objWriter->writeAttribute('xmlns:vt', 'http:

        
        $objWriter->writeElement('Application', 'Microsoft Excel');

        
        $objWriter->writeElement('DocSecurity', '0');

        
        $objWriter->writeElement('ScaleCrop', 'false');

        
        $objWriter->startElement('HeadingPairs');

        
        $objWriter->startElement('vt:vector');
        $objWriter->writeAttribute('size', '2');
        $objWriter->writeAttribute('baseType', 'variant');

        
        $objWriter->startElement('vt:variant');
        $objWriter->writeElement('vt:lpstr', 'Worksheets');
        $objWriter->endElement();

        
        $objWriter->startElement('vt:variant');
        $objWriter->writeElement('vt:i4', $spreadsheet->getSheetCount());
        $objWriter->endElement();

        $objWriter->endElement();

        $objWriter->endElement();

        
        $objWriter->startElement('TitlesOfParts');

        
        $objWriter->startElement('vt:vector');
        $objWriter->writeAttribute('size', $spreadsheet->getSheetCount());
        $objWriter->writeAttribute('baseType', 'lpstr');

        $sheetCount = $spreadsheet->getSheetCount();
        for ($i = 0; $i < $sheetCount; ++$i) {
            $objWriter->writeElement('vt:lpstr', $spreadsheet->getSheet($i)->getTitle());
        }

        $objWriter->endElement();

        $objWriter->endElement();

        
        $objWriter->writeElement('Company', $spreadsheet->getProperties()->getCompany());

        
        $objWriter->writeElement('Manager', $spreadsheet->getProperties()->getManager());

        
        $objWriter->writeElement('LinksUpToDate', 'false');

        
        $objWriter->writeElement('SharedDoc', 'false');

        
        $objWriter->writeElement('HyperlinksChanged', 'false');

        
        $objWriter->writeElement('AppVersion', '12.0000');

        $objWriter->endElement();

        
        return $objWriter->getData();
    }

    
    public function writeDocPropsCore(Spreadsheet $spreadsheet)
    {
        
        $objWriter = null;
        if ($this->getParentWriter()->getUseDiskCaching()) {
            $objWriter = new XMLWriter(XMLWriter::STORAGE_DISK, $this->getParentWriter()->getDiskCachingDirectory());
        } else {
            $objWriter = new XMLWriter(XMLWriter::STORAGE_MEMORY);
        }

        
        $objWriter->startDocument('1.0', 'UTF-8', 'yes');

        
        $objWriter->startElement('cp:coreProperties');
        $objWriter->writeAttribute('xmlns:cp', 'http:
        $objWriter->writeAttribute('xmlns:dc', 'http:
        $objWriter->writeAttribute('xmlns:dcterms', 'http:
        $objWriter->writeAttribute('xmlns:dcmitype', 'http:
        $objWriter->writeAttribute('xmlns:xsi', 'http:

        
        $objWriter->writeElement('dc:creator', $spreadsheet->getProperties()->getCreator());

        
        $objWriter->writeElement('cp:lastModifiedBy', $spreadsheet->getProperties()->getLastModifiedBy());

        
        $objWriter->startElement('dcterms:created');
        $objWriter->writeAttribute('xsi:type', 'dcterms:W3CDTF');
        $objWriter->writeRawData(date(DATE_W3C, $spreadsheet->getProperties()->getCreated()));
        $objWriter->endElement();

        
        $objWriter->startElement('dcterms:modified');
        $objWriter->writeAttribute('xsi:type', 'dcterms:W3CDTF');
        $objWriter->writeRawData(date(DATE_W3C, $spreadsheet->getProperties()->getModified()));
        $objWriter->endElement();

        
        $objWriter->writeElement('dc:title', $spreadsheet->getProperties()->getTitle());

        
        $objWriter->writeElement('dc:description', $spreadsheet->getProperties()->getDescription());

        
        $objWriter->writeElement('dc:subject', $spreadsheet->getProperties()->getSubject());

        
        $objWriter->writeElement('cp:keywords', $spreadsheet->getProperties()->getKeywords());

        
        $objWriter->writeElement('cp:category', $spreadsheet->getProperties()->getCategory());

        $objWriter->endElement();

        
        return $objWriter->getData();
    }

    
    public function writeDocPropsCustom(Spreadsheet $spreadsheet)
    {
        $customPropertyList = $spreadsheet->getProperties()->getCustomProperties();
        if (empty($customPropertyList)) {
            return;
        }

        
        $objWriter = null;
        if ($this->getParentWriter()->getUseDiskCaching()) {
            $objWriter = new XMLWriter(XMLWriter::STORAGE_DISK, $this->getParentWriter()->getDiskCachingDirectory());
        } else {
            $objWriter = new XMLWriter(XMLWriter::STORAGE_MEMORY);
        }

        
        $objWriter->startDocument('1.0', 'UTF-8', 'yes');

        
        $objWriter->startElement('Properties');
        $objWriter->writeAttribute('xmlns', 'http:
        $objWriter->writeAttribute('xmlns:vt', 'http:

        foreach ($customPropertyList as $key => $customProperty) {
            $propertyValue = $spreadsheet->getProperties()->getCustomPropertyValue($customProperty);
            $propertyType = $spreadsheet->getProperties()->getCustomPropertyType($customProperty);

            $objWriter->startElement('property');
            $objWriter->writeAttribute('fmtid', '{D5CDD505-2E9C-101B-9397-08002B2CF9AE}');
            $objWriter->writeAttribute('pid', $key + 2);
            $objWriter->writeAttribute('name', $customProperty);

            switch ($propertyType) {
                case 'i':
                    $objWriter->writeElement('vt:i4', $propertyValue);

                    break;
                case 'f':
                    $objWriter->writeElement('vt:r8', $propertyValue);

                    break;
                case 'b':
                    $objWriter->writeElement('vt:bool', ($propertyValue) ? 'true' : 'false');

                    break;
                case 'd':
                    $objWriter->startElement('vt:filetime');
                    $objWriter->writeRawData(date(DATE_W3C, $propertyValue));
                    $objWriter->endElement();

                    break;
                default:
                    $objWriter->writeElement('vt:lpwstr', $propertyValue);

                    break;
            }

            $objWriter->endElement();
        }

        $objWriter->endElement();

        return $objWriter->getData();
    }
}
