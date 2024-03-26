<?php

namespace PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Shared\XMLWriter;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing;
use PhpOffice\PhpSpreadsheet\Writer\Exception as WriterException;

class ContentTypes extends WriterPart
{
    
    public function writeContentTypes(Spreadsheet $spreadsheet, $includeCharts = false)
    {
        
        $objWriter = null;
        if ($this->getParentWriter()->getUseDiskCaching()) {
            $objWriter = new XMLWriter(XMLWriter::STORAGE_DISK, $this->getParentWriter()->getDiskCachingDirectory());
        } else {
            $objWriter = new XMLWriter(XMLWriter::STORAGE_MEMORY);
        }

        
        $objWriter->startDocument('1.0', 'UTF-8', 'yes');

        
        $objWriter->startElement('Types');
        $objWriter->writeAttribute('xmlns', 'http:

        
        $this->writeOverrideContentType($objWriter, '/xl/theme/theme1.xml', 'application/vnd.openxmlformats-officedocument.theme+xml');

        
        $this->writeOverrideContentType($objWriter, '/xl/styles.xml', 'application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml');

        
        $this->writeDefaultContentType($objWriter, 'rels', 'application/vnd.openxmlformats-package.relationships+xml');

        
        $this->writeDefaultContentType($objWriter, 'xml', 'application/xml');

        
        $this->writeDefaultContentType($objWriter, 'vml', 'application/vnd.openxmlformats-officedocument.vmlDrawing');

        
        if ($spreadsheet->hasMacros()) { 
            
            $this->writeOverrideContentType($objWriter, '/xl/workbook.xml', 'application/vnd.ms-excel.sheet.macroEnabled.main+xml');
            
            
            $this->writeOverrideContentType($objWriter, '/xl/vbaProject.bin', 'application/vnd.ms-office.vbaProject');
            if ($spreadsheet->hasMacrosCertificate()) {
                
                
                $this->writeOverrideContentType($objWriter, '/xl/vbaProjectSignature.bin', 'application/vnd.ms-office.vbaProjectSignature');
            }
        } else {
            
            $this->writeOverrideContentType($objWriter, '/xl/workbook.xml', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml');
        }

        
        $this->writeOverrideContentType($objWriter, '/docProps/app.xml', 'application/vnd.openxmlformats-officedocument.extended-properties+xml');

        $this->writeOverrideContentType($objWriter, '/docProps/core.xml', 'application/vnd.openxmlformats-package.core-properties+xml');

        $customPropertyList = $spreadsheet->getProperties()->getCustomProperties();
        if (!empty($customPropertyList)) {
            $this->writeOverrideContentType($objWriter, '/docProps/custom.xml', 'application/vnd.openxmlformats-officedocument.custom-properties+xml');
        }

        
        $sheetCount = $spreadsheet->getSheetCount();
        for ($i = 0; $i < $sheetCount; ++$i) {
            $this->writeOverrideContentType($objWriter, '/xl/worksheets/sheet' . ($i + 1) . '.xml', 'application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml');
        }

        
        $this->writeOverrideContentType($objWriter, '/xl/sharedStrings.xml', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sharedStrings+xml');

        
        $unparsedLoadedData = $spreadsheet->getUnparsedLoadedData();
        $chart = 1;
        for ($i = 0; $i < $sheetCount; ++$i) {
            $drawings = $spreadsheet->getSheet($i)->getDrawingCollection();
            $drawingCount = count($drawings);
            $chartCount = ($includeCharts) ? $spreadsheet->getSheet($i)->getChartCount() : 0;
            $hasUnparsedDrawing = isset($unparsedLoadedData['sheets'][$spreadsheet->getSheet($i)->getCodeName()]['drawingOriginalIds']);

            
            if (($drawingCount > 0) || ($chartCount > 0) || $hasUnparsedDrawing) {
                $this->writeOverrideContentType($objWriter, '/xl/drawings/drawing' . ($i + 1) . '.xml', 'application/vnd.openxmlformats-officedocument.drawing+xml');
            }

            
            if ($chartCount > 0) {
                for ($c = 0; $c < $chartCount; ++$c) {
                    $this->writeOverrideContentType($objWriter, '/xl/charts/chart' . $chart++ . '.xml', 'application/vnd.openxmlformats-officedocument.drawingml.chart+xml');
                }
            }
        }

        
        for ($i = 0; $i < $sheetCount; ++$i) {
            if (count($spreadsheet->getSheet($i)->getComments()) > 0) {
                $this->writeOverrideContentType($objWriter, '/xl/comments' . ($i + 1) . '.xml', 'application/vnd.openxmlformats-officedocument.spreadsheetml.comments+xml');
            }
        }

        
        $aMediaContentTypes = [];
        $mediaCount = $this->getParentWriter()->getDrawingHashTable()->count();
        for ($i = 0; $i < $mediaCount; ++$i) {
            $extension = '';
            $mimeType = '';

            if ($this->getParentWriter()->getDrawingHashTable()->getByIndex($i) instanceof \PhpOffice\PhpSpreadsheet\Worksheet\Drawing) {
                $extension = strtolower($this->getParentWriter()->getDrawingHashTable()->getByIndex($i)->getExtension());
                $mimeType = $this->getImageMimeType($this->getParentWriter()->getDrawingHashTable()->getByIndex($i)->getPath());
            } elseif ($this->getParentWriter()->getDrawingHashTable()->getByIndex($i) instanceof MemoryDrawing) {
                $extension = strtolower($this->getParentWriter()->getDrawingHashTable()->getByIndex($i)->getMimeType());
                $extension = explode('/', $extension);
                $extension = $extension[1];

                $mimeType = $this->getParentWriter()->getDrawingHashTable()->getByIndex($i)->getMimeType();
            }

            if (!isset($aMediaContentTypes[$extension])) {
                $aMediaContentTypes[$extension] = $mimeType;

                $this->writeDefaultContentType($objWriter, $extension, $mimeType);
            }
        }
        if ($spreadsheet->hasRibbonBinObjects()) {
            
            
            $tabRibbonTypes = array_diff($spreadsheet->getRibbonBinObjects('types'), array_keys($aMediaContentTypes));
            foreach ($tabRibbonTypes as $aRibbonType) {
                $mimeType = 'image/.' . $aRibbonType; 
                $this->writeDefaultContentType($objWriter, $aRibbonType, $mimeType);
            }
        }
        $sheetCount = $spreadsheet->getSheetCount();
        for ($i = 0; $i < $sheetCount; ++$i) {
            if (count($spreadsheet->getSheet($i)->getHeaderFooter()->getImages()) > 0) {
                foreach ($spreadsheet->getSheet($i)->getHeaderFooter()->getImages() as $image) {
                    if (!isset($aMediaContentTypes[strtolower($image->getExtension())])) {
                        $aMediaContentTypes[strtolower($image->getExtension())] = $this->getImageMimeType($image->getPath());

                        $this->writeDefaultContentType($objWriter, strtolower($image->getExtension()), $aMediaContentTypes[strtolower($image->getExtension())]);
                    }
                }
            }
        }

        
        if (isset($unparsedLoadedData['default_content_types'])) {
            foreach ($unparsedLoadedData['default_content_types'] as $extName => $contentType) {
                $this->writeDefaultContentType($objWriter, $extName, $contentType);
            }
        }

        
        if (isset($unparsedLoadedData['override_content_types'])) {
            foreach ($unparsedLoadedData['override_content_types'] as $partName => $overrideType) {
                $this->writeOverrideContentType($objWriter, $partName, $overrideType);
            }
        }

        $objWriter->endElement();

        
        return $objWriter->getData();
    }

    
    private function getImageMimeType($pFile)
    {
        if (File::fileExists($pFile)) {
            $image = getimagesize($pFile);

            return image_type_to_mime_type($image[2]);
        }

        throw new WriterException("File $pFile does not exist");
    }

    
    private function writeDefaultContentType(XMLWriter $objWriter, $pPartname, $pContentType): void
    {
        if ($pPartname != '' && $pContentType != '') {
            
            $objWriter->startElement('Default');
            $objWriter->writeAttribute('Extension', $pPartname);
            $objWriter->writeAttribute('ContentType', $pContentType);
            $objWriter->endElement();
        } else {
            throw new WriterException('Invalid parameters passed.');
        }
    }

    
    private function writeOverrideContentType(XMLWriter $objWriter, $pPartname, $pContentType): void
    {
        if ($pPartname != '' && $pContentType != '') {
            
            $objWriter->startElement('Override');
            $objWriter->writeAttribute('PartName', $pPartname);
            $objWriter->writeAttribute('ContentType', $pContentType);
            $objWriter->endElement();
        } else {
            throw new WriterException('Invalid parameters passed.');
        }
    }
}
