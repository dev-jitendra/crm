<?php

namespace PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Shared\XMLWriter;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Exception as WriterException;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\DefinedNames as DefinedNamesWriter;

class Workbook extends WriterPart
{
    
    public function writeWorkbook(Spreadsheet $spreadsheet, $recalcRequired = false)
    {
        
        if ($this->getParentWriter()->getUseDiskCaching()) {
            $objWriter = new XMLWriter(XMLWriter::STORAGE_DISK, $this->getParentWriter()->getDiskCachingDirectory());
        } else {
            $objWriter = new XMLWriter(XMLWriter::STORAGE_MEMORY);
        }

        
        $objWriter->startDocument('1.0', 'UTF-8', 'yes');

        
        $objWriter->startElement('workbook');
        $objWriter->writeAttribute('xml:space', 'preserve');
        $objWriter->writeAttribute('xmlns', 'http:
        $objWriter->writeAttribute('xmlns:r', 'http:

        
        $this->writeFileVersion($objWriter);

        
        $this->writeWorkbookPr($objWriter);

        
        $this->writeWorkbookProtection($objWriter, $spreadsheet);

        
        if ($this->getParentWriter()->getOffice2003Compatibility() === false) {
            $this->writeBookViews($objWriter, $spreadsheet);
        }

        
        $this->writeSheets($objWriter, $spreadsheet);

        
        (new DefinedNamesWriter($objWriter, $spreadsheet))->write();

        
        $this->writeCalcPr($objWriter, $recalcRequired);

        $objWriter->endElement();

        
        return $objWriter->getData();
    }

    
    private function writeFileVersion(XMLWriter $objWriter): void
    {
        $objWriter->startElement('fileVersion');
        $objWriter->writeAttribute('appName', 'xl');
        $objWriter->writeAttribute('lastEdited', '4');
        $objWriter->writeAttribute('lowestEdited', '4');
        $objWriter->writeAttribute('rupBuild', '4505');
        $objWriter->endElement();
    }

    
    private function writeWorkbookPr(XMLWriter $objWriter): void
    {
        $objWriter->startElement('workbookPr');

        if (Date::getExcelCalendar() === Date::CALENDAR_MAC_1904) {
            $objWriter->writeAttribute('date1904', '1');
        }

        $objWriter->writeAttribute('codeName', 'ThisWorkbook');

        $objWriter->endElement();
    }

    
    private function writeBookViews(XMLWriter $objWriter, Spreadsheet $spreadsheet): void
    {
        
        $objWriter->startElement('bookViews');

        
        $objWriter->startElement('workbookView');

        $objWriter->writeAttribute('activeTab', $spreadsheet->getActiveSheetIndex());
        $objWriter->writeAttribute('autoFilterDateGrouping', ($spreadsheet->getAutoFilterDateGrouping() ? 'true' : 'false'));
        $objWriter->writeAttribute('firstSheet', $spreadsheet->getFirstSheetIndex());
        $objWriter->writeAttribute('minimized', ($spreadsheet->getMinimized() ? 'true' : 'false'));
        $objWriter->writeAttribute('showHorizontalScroll', ($spreadsheet->getShowHorizontalScroll() ? 'true' : 'false'));
        $objWriter->writeAttribute('showSheetTabs', ($spreadsheet->getShowSheetTabs() ? 'true' : 'false'));
        $objWriter->writeAttribute('showVerticalScroll', ($spreadsheet->getShowVerticalScroll() ? 'true' : 'false'));
        $objWriter->writeAttribute('tabRatio', $spreadsheet->getTabRatio());
        $objWriter->writeAttribute('visibility', $spreadsheet->getVisibility());

        $objWriter->endElement();

        $objWriter->endElement();
    }

    
    private function writeWorkbookProtection(XMLWriter $objWriter, Spreadsheet $spreadsheet): void
    {
        if ($spreadsheet->getSecurity()->isSecurityEnabled()) {
            $objWriter->startElement('workbookProtection');
            $objWriter->writeAttribute('lockRevision', ($spreadsheet->getSecurity()->getLockRevision() ? 'true' : 'false'));
            $objWriter->writeAttribute('lockStructure', ($spreadsheet->getSecurity()->getLockStructure() ? 'true' : 'false'));
            $objWriter->writeAttribute('lockWindows', ($spreadsheet->getSecurity()->getLockWindows() ? 'true' : 'false'));

            if ($spreadsheet->getSecurity()->getRevisionsPassword() != '') {
                $objWriter->writeAttribute('revisionsPassword', $spreadsheet->getSecurity()->getRevisionsPassword());
            }

            if ($spreadsheet->getSecurity()->getWorkbookPassword() != '') {
                $objWriter->writeAttribute('workbookPassword', $spreadsheet->getSecurity()->getWorkbookPassword());
            }

            $objWriter->endElement();
        }
    }

    
    private function writeCalcPr(XMLWriter $objWriter, $recalcRequired = true): void
    {
        $objWriter->startElement('calcPr');

        
        
        
        $objWriter->writeAttribute('calcId', '999999');
        $objWriter->writeAttribute('calcMode', 'auto');
        
        $objWriter->writeAttribute('calcCompleted', ($recalcRequired) ? 1 : 0);
        $objWriter->writeAttribute('fullCalcOnLoad', ($recalcRequired) ? 0 : 1);
        $objWriter->writeAttribute('forceFullCalc', ($recalcRequired) ? 0 : 1);

        $objWriter->endElement();
    }

    
    private function writeSheets(XMLWriter $objWriter, Spreadsheet $spreadsheet): void
    {
        
        $objWriter->startElement('sheets');
        $sheetCount = $spreadsheet->getSheetCount();
        for ($i = 0; $i < $sheetCount; ++$i) {
            
            $this->writeSheet(
                $objWriter,
                $spreadsheet->getSheet($i)->getTitle(),
                ($i + 1),
                ($i + 1 + 3),
                $spreadsheet->getSheet($i)->getSheetState()
            );
        }

        $objWriter->endElement();
    }

    
    private function writeSheet(XMLWriter $objWriter, $pSheetname, $pSheetId = 1, $pRelId = 1, $sheetState = 'visible'): void
    {
        if ($pSheetname != '') {
            
            $objWriter->startElement('sheet');
            $objWriter->writeAttribute('name', $pSheetname);
            $objWriter->writeAttribute('sheetId', $pSheetId);
            if ($sheetState !== 'visible' && $sheetState != '') {
                $objWriter->writeAttribute('state', $sheetState);
            }
            $objWriter->writeAttribute('r:id', 'rId' . $pRelId);
            $objWriter->endElement();
        } else {
            throw new WriterException('Invalid parameters passed.');
        }
    }
}
