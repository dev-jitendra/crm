<?php

namespace PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Shared\XMLWriter;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing;
use PhpOffice\PhpSpreadsheet\Writer\Exception as WriterException;

class Rels extends WriterPart
{
    
    public function writeRelationships(Spreadsheet $spreadsheet)
    {
        
        $objWriter = null;
        if ($this->getParentWriter()->getUseDiskCaching()) {
            $objWriter = new XMLWriter(XMLWriter::STORAGE_DISK, $this->getParentWriter()->getDiskCachingDirectory());
        } else {
            $objWriter = new XMLWriter(XMLWriter::STORAGE_MEMORY);
        }

        
        $objWriter->startDocument('1.0', 'UTF-8', 'yes');

        
        $objWriter->startElement('Relationships');
        $objWriter->writeAttribute('xmlns', 'http:

        $customPropertyList = $spreadsheet->getProperties()->getCustomProperties();
        if (!empty($customPropertyList)) {
            
            $this->writeRelationship(
                $objWriter,
                4,
                'http:
                'docProps/custom.xml'
            );
        }

        
        $this->writeRelationship(
            $objWriter,
            3,
            'http:
            'docProps/app.xml'
        );

        
        $this->writeRelationship(
            $objWriter,
            2,
            'http:
            'docProps/core.xml'
        );

        
        $this->writeRelationship(
            $objWriter,
            1,
            'http:
            'xl/workbook.xml'
        );
        
        if ($spreadsheet->hasRibbon()) {
            $this->writeRelationShip(
                $objWriter,
                5,
                'http:
                $spreadsheet->getRibbonXMLData('target')
            );
        }

        $objWriter->endElement();

        return $objWriter->getData();
    }

    
    public function writeWorkbookRelationships(Spreadsheet $spreadsheet)
    {
        
        $objWriter = null;
        if ($this->getParentWriter()->getUseDiskCaching()) {
            $objWriter = new XMLWriter(XMLWriter::STORAGE_DISK, $this->getParentWriter()->getDiskCachingDirectory());
        } else {
            $objWriter = new XMLWriter(XMLWriter::STORAGE_MEMORY);
        }

        
        $objWriter->startDocument('1.0', 'UTF-8', 'yes');

        
        $objWriter->startElement('Relationships');
        $objWriter->writeAttribute('xmlns', 'http:

        
        $this->writeRelationship(
            $objWriter,
            1,
            'http:
            'styles.xml'
        );

        
        $this->writeRelationship(
            $objWriter,
            2,
            'http:
            'theme/theme1.xml'
        );

        
        $this->writeRelationship(
            $objWriter,
            3,
            'http:
            'sharedStrings.xml'
        );

        
        $sheetCount = $spreadsheet->getSheetCount();
        for ($i = 0; $i < $sheetCount; ++$i) {
            $this->writeRelationship(
                $objWriter,
                ($i + 1 + 3),
                'http:
                'worksheets/sheet' . ($i + 1) . '.xml'
            );
        }
        
        
        if ($spreadsheet->hasMacros()) {
            $this->writeRelationShip(
                $objWriter,
                ($i + 1 + 3),
                'http:
                'vbaProject.bin'
            );
            ++$i; 
        }

        $objWriter->endElement();

        return $objWriter->getData();
    }

    
    public function writeWorksheetRelationships(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $pWorksheet, $pWorksheetId = 1, $includeCharts = false)
    {
        
        $objWriter = null;
        if ($this->getParentWriter()->getUseDiskCaching()) {
            $objWriter = new XMLWriter(XMLWriter::STORAGE_DISK, $this->getParentWriter()->getDiskCachingDirectory());
        } else {
            $objWriter = new XMLWriter(XMLWriter::STORAGE_MEMORY);
        }

        
        $objWriter->startDocument('1.0', 'UTF-8', 'yes');

        
        $objWriter->startElement('Relationships');
        $objWriter->writeAttribute('xmlns', 'http:

        
        $drawingOriginalIds = [];
        $unparsedLoadedData = $pWorksheet->getParent()->getUnparsedLoadedData();
        if (isset($unparsedLoadedData['sheets'][$pWorksheet->getCodeName()]['drawingOriginalIds'])) {
            $drawingOriginalIds = $unparsedLoadedData['sheets'][$pWorksheet->getCodeName()]['drawingOriginalIds'];
        }

        if ($includeCharts) {
            $charts = $pWorksheet->getChartCollection();
        } else {
            $charts = [];
        }

        if (($pWorksheet->getDrawingCollection()->count() > 0) || (count($charts) > 0) || $drawingOriginalIds) {
            $rId = 1;

            
            
            
            reset($drawingOriginalIds);
            $relPath = key($drawingOriginalIds);
            if (isset($drawingOriginalIds[$relPath])) {
                $rId = (int) (substr($drawingOriginalIds[$relPath], 3));
            }

            
            $relPath = '../drawings/drawing' . $pWorksheetId . '.xml';
            $this->writeRelationship(
                $objWriter,
                $rId,
                'http:
                $relPath
            );
        }

        
        $i = 1;
        foreach ($pWorksheet->getHyperlinkCollection() as $hyperlink) {
            if (!$hyperlink->isInternal()) {
                $this->writeRelationship(
                    $objWriter,
                    '_hyperlink_' . $i,
                    'http:
                    $hyperlink->getUrl(),
                    'External'
                );

                ++$i;
            }
        }

        
        $i = 1;
        if (count($pWorksheet->getComments()) > 0) {
            $this->writeRelationship(
                $objWriter,
                '_comments_vml' . $i,
                'http:
                '../drawings/vmlDrawing' . $pWorksheetId . '.vml'
            );

            $this->writeRelationship(
                $objWriter,
                '_comments' . $i,
                'http:
                '../comments' . $pWorksheetId . '.xml'
            );
        }

        
        $i = 1;
        if (count($pWorksheet->getHeaderFooter()->getImages()) > 0) {
            $this->writeRelationship(
                $objWriter,
                '_headerfooter_vml' . $i,
                'http:
                '../drawings/vmlDrawingHF' . $pWorksheetId . '.vml'
            );
        }

        $this->writeUnparsedRelationship($pWorksheet, $objWriter, 'ctrlProps', 'http:
        $this->writeUnparsedRelationship($pWorksheet, $objWriter, 'vmlDrawings', 'http:
        $this->writeUnparsedRelationship($pWorksheet, $objWriter, 'printerSettings', 'http:

        $objWriter->endElement();

        return $objWriter->getData();
    }

    private function writeUnparsedRelationship(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $pWorksheet, XMLWriter $objWriter, $relationship, $type): void
    {
        $unparsedLoadedData = $pWorksheet->getParent()->getUnparsedLoadedData();
        if (!isset($unparsedLoadedData['sheets'][$pWorksheet->getCodeName()][$relationship])) {
            return;
        }

        foreach ($unparsedLoadedData['sheets'][$pWorksheet->getCodeName()][$relationship] as $rId => $value) {
            $this->writeRelationship(
                $objWriter,
                $rId,
                $type,
                $value['relFilePath']
            );
        }
    }

    
    public function writeDrawingRelationships(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $pWorksheet, &$chartRef, $includeCharts = false)
    {
        
        $objWriter = null;
        if ($this->getParentWriter()->getUseDiskCaching()) {
            $objWriter = new XMLWriter(XMLWriter::STORAGE_DISK, $this->getParentWriter()->getDiskCachingDirectory());
        } else {
            $objWriter = new XMLWriter(XMLWriter::STORAGE_MEMORY);
        }

        
        $objWriter->startDocument('1.0', 'UTF-8', 'yes');

        
        $objWriter->startElement('Relationships');
        $objWriter->writeAttribute('xmlns', 'http:

        
        $i = 1;
        $iterator = $pWorksheet->getDrawingCollection()->getIterator();
        while ($iterator->valid()) {
            if (
                $iterator->current() instanceof \PhpOffice\PhpSpreadsheet\Worksheet\Drawing
                || $iterator->current() instanceof MemoryDrawing
            ) {
                
                
                $drawing = $iterator->current();
                $this->writeRelationship(
                    $objWriter,
                    $i,
                    'http:
                    '../media/' . str_replace(' ', '', $drawing->getIndexedFilename())
                );

                $i = $this->writeDrawingHyperLink($objWriter, $drawing, $i);
            }

            $iterator->next();
            ++$i;
        }

        if ($includeCharts) {
            
            $chartCount = $pWorksheet->getChartCount();
            if ($chartCount > 0) {
                for ($c = 0; $c < $chartCount; ++$c) {
                    $this->writeRelationship(
                        $objWriter,
                        $i++,
                        'http:
                        '../charts/chart' . ++$chartRef . '.xml'
                    );
                }
            }
        }

        $objWriter->endElement();

        return $objWriter->getData();
    }

    
    public function writeHeaderFooterDrawingRelationships(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $pWorksheet)
    {
        
        $objWriter = null;
        if ($this->getParentWriter()->getUseDiskCaching()) {
            $objWriter = new XMLWriter(XMLWriter::STORAGE_DISK, $this->getParentWriter()->getDiskCachingDirectory());
        } else {
            $objWriter = new XMLWriter(XMLWriter::STORAGE_MEMORY);
        }

        
        $objWriter->startDocument('1.0', 'UTF-8', 'yes');

        
        $objWriter->startElement('Relationships');
        $objWriter->writeAttribute('xmlns', 'http:

        
        foreach ($pWorksheet->getHeaderFooter()->getImages() as $key => $value) {
            
            $this->writeRelationship(
                $objWriter,
                $key,
                'http:
                '../media/' . $value->getIndexedFilename()
            );
        }

        $objWriter->endElement();

        return $objWriter->getData();
    }

    
    private function writeRelationship(XMLWriter $objWriter, $pId, $pType, $pTarget, $pTargetMode = ''): void
    {
        if ($pType != '' && $pTarget != '') {
            
            $objWriter->startElement('Relationship');
            $objWriter->writeAttribute('Id', 'rId' . $pId);
            $objWriter->writeAttribute('Type', $pType);
            $objWriter->writeAttribute('Target', $pTarget);

            if ($pTargetMode != '') {
                $objWriter->writeAttribute('TargetMode', $pTargetMode);
            }

            $objWriter->endElement();
        } else {
            throw new WriterException('Invalid parameters passed.');
        }
    }

    
    private function writeDrawingHyperLink($objWriter, $drawing, $i)
    {
        if ($drawing->getHyperlink() === null) {
            return $i;
        }

        ++$i;
        $this->writeRelationship(
            $objWriter,
            $i,
            'http:
            $drawing->getHyperlink()->getUrl(),
            $drawing->getHyperlink()->getTypeHyperlink()
        );

        return $i;
    }
}
