<?php

namespace PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Shared\XMLWriter;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\BaseDrawing;
use PhpOffice\PhpSpreadsheet\Worksheet\HeaderFooterDrawing;
use PhpOffice\PhpSpreadsheet\Writer\Exception as WriterException;

class Drawing extends WriterPart
{
    
    public function writeDrawings(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $pWorksheet, $includeCharts = false)
    {
        
        $objWriter = null;
        if ($this->getParentWriter()->getUseDiskCaching()) {
            $objWriter = new XMLWriter(XMLWriter::STORAGE_DISK, $this->getParentWriter()->getDiskCachingDirectory());
        } else {
            $objWriter = new XMLWriter(XMLWriter::STORAGE_MEMORY);
        }

        
        $objWriter->startDocument('1.0', 'UTF-8', 'yes');

        
        $objWriter->startElement('xdr:wsDr');
        $objWriter->writeAttribute('xmlns:xdr', 'http:
        $objWriter->writeAttribute('xmlns:a', 'http:

        
        $i = 1;
        $iterator = $pWorksheet->getDrawingCollection()->getIterator();
        while ($iterator->valid()) {
            
            $pDrawing = $iterator->current();
            $pRelationId = $i;
            $hlinkClickId = $pDrawing->getHyperlink() === null ? null : ++$i;

            $this->writeDrawing($objWriter, $pDrawing, $pRelationId, $hlinkClickId);

            $iterator->next();
            ++$i;
        }

        if ($includeCharts) {
            $chartCount = $pWorksheet->getChartCount();
            
            if ($chartCount > 0) {
                for ($c = 0; $c < $chartCount; ++$c) {
                    $this->writeChart($objWriter, $pWorksheet->getChartByIndex($c), $c + $i);
                }
            }
        }

        
        $unparsedLoadedData = $pWorksheet->getParent()->getUnparsedLoadedData();
        if (isset($unparsedLoadedData['sheets'][$pWorksheet->getCodeName()]['drawingAlternateContents'])) {
            foreach ($unparsedLoadedData['sheets'][$pWorksheet->getCodeName()]['drawingAlternateContents'] as $drawingAlternateContent) {
                $objWriter->writeRaw($drawingAlternateContent);
            }
        }

        $objWriter->endElement();

        
        return $objWriter->getData();
    }

    
    public function writeChart(XMLWriter $objWriter, \PhpOffice\PhpSpreadsheet\Chart\Chart $pChart, $pRelationId = -1): void
    {
        $tl = $pChart->getTopLeftPosition();
        $tl['colRow'] = Coordinate::coordinateFromString($tl['cell']);
        $br = $pChart->getBottomRightPosition();
        $br['colRow'] = Coordinate::coordinateFromString($br['cell']);

        $objWriter->startElement('xdr:twoCellAnchor');

        $objWriter->startElement('xdr:from');
        $objWriter->writeElement('xdr:col', Coordinate::columnIndexFromString($tl['colRow'][0]) - 1);
        $objWriter->writeElement('xdr:colOff', \PhpOffice\PhpSpreadsheet\Shared\Drawing::pixelsToEMU($tl['xOffset']));
        $objWriter->writeElement('xdr:row', $tl['colRow'][1] - 1);
        $objWriter->writeElement('xdr:rowOff', \PhpOffice\PhpSpreadsheet\Shared\Drawing::pixelsToEMU($tl['yOffset']));
        $objWriter->endElement();
        $objWriter->startElement('xdr:to');
        $objWriter->writeElement('xdr:col', Coordinate::columnIndexFromString($br['colRow'][0]) - 1);
        $objWriter->writeElement('xdr:colOff', \PhpOffice\PhpSpreadsheet\Shared\Drawing::pixelsToEMU($br['xOffset']));
        $objWriter->writeElement('xdr:row', $br['colRow'][1] - 1);
        $objWriter->writeElement('xdr:rowOff', \PhpOffice\PhpSpreadsheet\Shared\Drawing::pixelsToEMU($br['yOffset']));
        $objWriter->endElement();

        $objWriter->startElement('xdr:graphicFrame');
        $objWriter->writeAttribute('macro', '');
        $objWriter->startElement('xdr:nvGraphicFramePr');
        $objWriter->startElement('xdr:cNvPr');
        $objWriter->writeAttribute('name', 'Chart ' . $pRelationId);
        $objWriter->writeAttribute('id', 1025 * $pRelationId);
        $objWriter->endElement();
        $objWriter->startElement('xdr:cNvGraphicFramePr');
        $objWriter->startElement('a:graphicFrameLocks');
        $objWriter->endElement();
        $objWriter->endElement();
        $objWriter->endElement();

        $objWriter->startElement('xdr:xfrm');
        $objWriter->startElement('a:off');
        $objWriter->writeAttribute('x', '0');
        $objWriter->writeAttribute('y', '0');
        $objWriter->endElement();
        $objWriter->startElement('a:ext');
        $objWriter->writeAttribute('cx', '0');
        $objWriter->writeAttribute('cy', '0');
        $objWriter->endElement();
        $objWriter->endElement();

        $objWriter->startElement('a:graphic');
        $objWriter->startElement('a:graphicData');
        $objWriter->writeAttribute('uri', 'http:
        $objWriter->startElement('c:chart');
        $objWriter->writeAttribute('xmlns:c', 'http:
        $objWriter->writeAttribute('xmlns:r', 'http:
        $objWriter->writeAttribute('r:id', 'rId' . $pRelationId);
        $objWriter->endElement();
        $objWriter->endElement();
        $objWriter->endElement();
        $objWriter->endElement();

        $objWriter->startElement('xdr:clientData');
        $objWriter->endElement();

        $objWriter->endElement();
    }

    
    public function writeDrawing(XMLWriter $objWriter, BaseDrawing $pDrawing, $pRelationId = -1, $hlinkClickId = null): void
    {
        if ($pRelationId >= 0) {
            
            $objWriter->startElement('xdr:oneCellAnchor');
            
            $aCoordinates = Coordinate::coordinateFromString($pDrawing->getCoordinates());
            $aCoordinates[0] = Coordinate::columnIndexFromString($aCoordinates[0]);

            
            $objWriter->startElement('xdr:from');
            $objWriter->writeElement('xdr:col', $aCoordinates[0] - 1);
            $objWriter->writeElement('xdr:colOff', \PhpOffice\PhpSpreadsheet\Shared\Drawing::pixelsToEMU($pDrawing->getOffsetX()));
            $objWriter->writeElement('xdr:row', $aCoordinates[1] - 1);
            $objWriter->writeElement('xdr:rowOff', \PhpOffice\PhpSpreadsheet\Shared\Drawing::pixelsToEMU($pDrawing->getOffsetY()));
            $objWriter->endElement();

            
            $objWriter->startElement('xdr:ext');
            $objWriter->writeAttribute('cx', \PhpOffice\PhpSpreadsheet\Shared\Drawing::pixelsToEMU($pDrawing->getWidth()));
            $objWriter->writeAttribute('cy', \PhpOffice\PhpSpreadsheet\Shared\Drawing::pixelsToEMU($pDrawing->getHeight()));
            $objWriter->endElement();

            
            $objWriter->startElement('xdr:pic');

            
            $objWriter->startElement('xdr:nvPicPr');

            
            $objWriter->startElement('xdr:cNvPr');
            $objWriter->writeAttribute('id', $pRelationId);
            $objWriter->writeAttribute('name', $pDrawing->getName());
            $objWriter->writeAttribute('descr', $pDrawing->getDescription());

            
            $this->writeHyperLinkDrawing($objWriter, $hlinkClickId);

            $objWriter->endElement();

            
            $objWriter->startElement('xdr:cNvPicPr');

            
            $objWriter->startElement('a:picLocks');
            $objWriter->writeAttribute('noChangeAspect', '1');
            $objWriter->endElement();

            $objWriter->endElement();

            $objWriter->endElement();

            
            $objWriter->startElement('xdr:blipFill');

            
            $objWriter->startElement('a:blip');
            $objWriter->writeAttribute('xmlns:r', 'http:
            $objWriter->writeAttribute('r:embed', 'rId' . $pRelationId);
            $objWriter->endElement();

            
            $objWriter->startElement('a:stretch');
            $objWriter->writeElement('a:fillRect', null);
            $objWriter->endElement();

            $objWriter->endElement();

            
            $objWriter->startElement('xdr:spPr');

            
            $objWriter->startElement('a:xfrm');
            $objWriter->writeAttribute('rot', \PhpOffice\PhpSpreadsheet\Shared\Drawing::degreesToAngle($pDrawing->getRotation()));
            $objWriter->endElement();

            
            $objWriter->startElement('a:prstGeom');
            $objWriter->writeAttribute('prst', 'rect');

            
            $objWriter->writeElement('a:avLst', null);

            $objWriter->endElement();

            if ($pDrawing->getShadow()->getVisible()) {
                
                $objWriter->startElement('a:effectLst');

                
                $objWriter->startElement('a:outerShdw');
                $objWriter->writeAttribute('blurRad', \PhpOffice\PhpSpreadsheet\Shared\Drawing::pixelsToEMU($pDrawing->getShadow()->getBlurRadius()));
                $objWriter->writeAttribute('dist', \PhpOffice\PhpSpreadsheet\Shared\Drawing::pixelsToEMU($pDrawing->getShadow()->getDistance()));
                $objWriter->writeAttribute('dir', \PhpOffice\PhpSpreadsheet\Shared\Drawing::degreesToAngle($pDrawing->getShadow()->getDirection()));
                $objWriter->writeAttribute('algn', $pDrawing->getShadow()->getAlignment());
                $objWriter->writeAttribute('rotWithShape', '0');

                
                $objWriter->startElement('a:srgbClr');
                $objWriter->writeAttribute('val', $pDrawing->getShadow()->getColor()->getRGB());

                
                $objWriter->startElement('a:alpha');
                $objWriter->writeAttribute('val', $pDrawing->getShadow()->getAlpha() * 1000);
                $objWriter->endElement();

                $objWriter->endElement();

                $objWriter->endElement();

                $objWriter->endElement();
            }
            $objWriter->endElement();

            $objWriter->endElement();

            
            $objWriter->writeElement('xdr:clientData', null);

            $objWriter->endElement();
        } else {
            throw new WriterException('Invalid parameters passed.');
        }
    }

    
    public function writeVMLHeaderFooterImages(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $pWorksheet)
    {
        
        $objWriter = null;
        if ($this->getParentWriter()->getUseDiskCaching()) {
            $objWriter = new XMLWriter(XMLWriter::STORAGE_DISK, $this->getParentWriter()->getDiskCachingDirectory());
        } else {
            $objWriter = new XMLWriter(XMLWriter::STORAGE_MEMORY);
        }

        
        $objWriter->startDocument('1.0', 'UTF-8', 'yes');

        
        $images = $pWorksheet->getHeaderFooter()->getImages();

        
        $objWriter->startElement('xml');
        $objWriter->writeAttribute('xmlns:v', 'urn:schemas-microsoft-com:vml');
        $objWriter->writeAttribute('xmlns:o', 'urn:schemas-microsoft-com:office:office');
        $objWriter->writeAttribute('xmlns:x', 'urn:schemas-microsoft-com:office:excel');

        
        $objWriter->startElement('o:shapelayout');
        $objWriter->writeAttribute('v:ext', 'edit');

        
        $objWriter->startElement('o:idmap');
        $objWriter->writeAttribute('v:ext', 'edit');
        $objWriter->writeAttribute('data', '1');
        $objWriter->endElement();

        $objWriter->endElement();

        
        $objWriter->startElement('v:shapetype');
        $objWriter->writeAttribute('id', '_x0000_t75');
        $objWriter->writeAttribute('coordsize', '21600,21600');
        $objWriter->writeAttribute('o:spt', '75');
        $objWriter->writeAttribute('o:preferrelative', 't');
        $objWriter->writeAttribute('path', 'm@4@5l@4@11@9@11@9@5xe');
        $objWriter->writeAttribute('filled', 'f');
        $objWriter->writeAttribute('stroked', 'f');

        
        $objWriter->startElement('v:stroke');
        $objWriter->writeAttribute('joinstyle', 'miter');
        $objWriter->endElement();

        
        $objWriter->startElement('v:formulas');

        
        $objWriter->startElement('v:f');
        $objWriter->writeAttribute('eqn', 'if lineDrawn pixelLineWidth 0');
        $objWriter->endElement();

        
        $objWriter->startElement('v:f');
        $objWriter->writeAttribute('eqn', 'sum @0 1 0');
        $objWriter->endElement();

        
        $objWriter->startElement('v:f');
        $objWriter->writeAttribute('eqn', 'sum 0 0 @1');
        $objWriter->endElement();

        
        $objWriter->startElement('v:f');
        $objWriter->writeAttribute('eqn', 'prod @2 1 2');
        $objWriter->endElement();

        
        $objWriter->startElement('v:f');
        $objWriter->writeAttribute('eqn', 'prod @3 21600 pixelWidth');
        $objWriter->endElement();

        
        $objWriter->startElement('v:f');
        $objWriter->writeAttribute('eqn', 'prod @3 21600 pixelHeight');
        $objWriter->endElement();

        
        $objWriter->startElement('v:f');
        $objWriter->writeAttribute('eqn', 'sum @0 0 1');
        $objWriter->endElement();

        
        $objWriter->startElement('v:f');
        $objWriter->writeAttribute('eqn', 'prod @6 1 2');
        $objWriter->endElement();

        
        $objWriter->startElement('v:f');
        $objWriter->writeAttribute('eqn', 'prod @7 21600 pixelWidth');
        $objWriter->endElement();

        
        $objWriter->startElement('v:f');
        $objWriter->writeAttribute('eqn', 'sum @8 21600 0');
        $objWriter->endElement();

        
        $objWriter->startElement('v:f');
        $objWriter->writeAttribute('eqn', 'prod @7 21600 pixelHeight');
        $objWriter->endElement();

        
        $objWriter->startElement('v:f');
        $objWriter->writeAttribute('eqn', 'sum @10 21600 0');
        $objWriter->endElement();

        $objWriter->endElement();

        
        $objWriter->startElement('v:path');
        $objWriter->writeAttribute('o:extrusionok', 'f');
        $objWriter->writeAttribute('gradientshapeok', 't');
        $objWriter->writeAttribute('o:connecttype', 'rect');
        $objWriter->endElement();

        
        $objWriter->startElement('o:lock');
        $objWriter->writeAttribute('v:ext', 'edit');
        $objWriter->writeAttribute('aspectratio', 't');
        $objWriter->endElement();

        $objWriter->endElement();

        
        foreach ($images as $key => $value) {
            $this->writeVMLHeaderFooterImage($objWriter, $key, $value);
        }

        $objWriter->endElement();

        
        return $objWriter->getData();
    }

    
    private function writeVMLHeaderFooterImage(XMLWriter $objWriter, $pReference, HeaderFooterDrawing $pImage): void
    {
        
        preg_match('{(\d+)}', md5($pReference), $m);
        $id = 1500 + (substr($m[1], 0, 2) * 1);

        
        $width = $pImage->getWidth();
        $height = $pImage->getHeight();
        $marginLeft = $pImage->getOffsetX();
        $marginTop = $pImage->getOffsetY();

        
        $objWriter->startElement('v:shape');
        $objWriter->writeAttribute('id', $pReference);
        $objWriter->writeAttribute('o:spid', '_x0000_s' . $id);
        $objWriter->writeAttribute('type', '#_x0000_t75');
        $objWriter->writeAttribute('style', "position:absolute;margin-left:{$marginLeft}px;margin-top:{$marginTop}px;width:{$width}px;height:{$height}px;z-index:1");

        
        $objWriter->startElement('v:imagedata');
        $objWriter->writeAttribute('o:relid', 'rId' . $pReference);
        $objWriter->writeAttribute('o:title', $pImage->getName());
        $objWriter->endElement();

        
        $objWriter->startElement('o:lock');
        $objWriter->writeAttribute('v:ext', 'edit');
        $objWriter->writeAttribute('textRotation', 't');
        $objWriter->endElement();

        $objWriter->endElement();
    }

    
    public function allDrawings(Spreadsheet $spreadsheet)
    {
        
        $aDrawings = [];

        
        $sheetCount = $spreadsheet->getSheetCount();
        for ($i = 0; $i < $sheetCount; ++$i) {
            
            $iterator = $spreadsheet->getSheet($i)->getDrawingCollection()->getIterator();
            while ($iterator->valid()) {
                $aDrawings[] = $iterator->current();

                $iterator->next();
            }
        }

        return $aDrawings;
    }

    
    private function writeHyperLinkDrawing(XMLWriter $objWriter, $hlinkClickId): void
    {
        if ($hlinkClickId === null) {
            return;
        }

        $objWriter->startElement('a:hlinkClick');
        $objWriter->writeAttribute('xmlns:r', 'http:
        $objWriter->writeAttribute('r:id', 'rId' . $hlinkClickId);
        $objWriter->endElement();
    }
}
