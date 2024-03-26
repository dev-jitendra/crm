<?php

namespace PhpOffice\PhpSpreadsheet\Writer;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\HashTable;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing as WorksheetDrawing;
use PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing;
use PhpOffice\PhpSpreadsheet\Writer\Exception as WriterException;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\Chart;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\Comments;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\ContentTypes;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\DocProps;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\Drawing;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\Rels;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\RelsRibbon;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\RelsVBA;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\StringTable;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\Style;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\Theme;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\Workbook;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\Worksheet;
use ZipArchive;
use ZipStream\Exception\OverflowException;
use ZipStream\Option\Archive;
use ZipStream\ZipStream;

class Xlsx extends BaseWriter
{
    
    private $office2003compatibility = false;

    
    private $writerParts = [];

    
    private $spreadSheet;

    
    private $stringTable = [];

    
    private $stylesConditionalHashTable;

    
    private $styleHashTable;

    
    private $fillHashTable;

    
    private $fontHashTable;

    
    private $bordersHashTable;

    
    private $numFmtHashTable;

    
    private $drawingHashTable;

    
    private $zip;

    
    public function __construct(Spreadsheet $spreadsheet)
    {
        
        $this->setSpreadsheet($spreadsheet);

        $writerPartsArray = [
            'stringtable' => StringTable::class,
            'contenttypes' => ContentTypes::class,
            'docprops' => DocProps::class,
            'rels' => Rels::class,
            'theme' => Theme::class,
            'style' => Style::class,
            'workbook' => Workbook::class,
            'worksheet' => Worksheet::class,
            'drawing' => Drawing::class,
            'comments' => Comments::class,
            'chart' => Chart::class,
            'relsvba' => RelsVBA::class,
            'relsribbonobjects' => RelsRibbon::class,
        ];

        
        
        foreach ($writerPartsArray as $writer => $class) {
            $this->writerParts[$writer] = new $class($this);
        }

        $hashTablesArray = ['stylesConditionalHashTable', 'fillHashTable', 'fontHashTable',
            'bordersHashTable', 'numFmtHashTable', 'drawingHashTable',
            'styleHashTable',
        ];

        
        foreach ($hashTablesArray as $tableName) {
            $this->$tableName = new HashTable();
        }
    }

    
    public function getWriterPart($pPartName)
    {
        if ($pPartName != '' && isset($this->writerParts[strtolower($pPartName)])) {
            return $this->writerParts[strtolower($pPartName)];
        }

        return null;
    }

    
    public function save($pFilename): void
    {
        
        $this->pathNames = [];
        $this->spreadSheet->garbageCollect();

        $this->openFileHandle($pFilename);

        $saveDebugLog = Calculation::getInstance($this->spreadSheet)->getDebugLog()->getWriteDebugLog();
        Calculation::getInstance($this->spreadSheet)->getDebugLog()->setWriteDebugLog(false);
        $saveDateReturnType = Functions::getReturnDateType();
        Functions::setReturnDateType(Functions::RETURNDATE_EXCEL);

        
        $this->stringTable = [];
        for ($i = 0; $i < $this->spreadSheet->getSheetCount(); ++$i) {
            $this->stringTable = $this->getWriterPart('StringTable')->createStringTable($this->spreadSheet->getSheet($i), $this->stringTable);
        }

        
        $this->styleHashTable->addFromSource($this->getWriterPart('Style')->allStyles($this->spreadSheet));
        $this->stylesConditionalHashTable->addFromSource($this->getWriterPart('Style')->allConditionalStyles($this->spreadSheet));
        $this->fillHashTable->addFromSource($this->getWriterPart('Style')->allFills($this->spreadSheet));
        $this->fontHashTable->addFromSource($this->getWriterPart('Style')->allFonts($this->spreadSheet));
        $this->bordersHashTable->addFromSource($this->getWriterPart('Style')->allBorders($this->spreadSheet));
        $this->numFmtHashTable->addFromSource($this->getWriterPart('Style')->allNumberFormats($this->spreadSheet));

        
        $this->drawingHashTable->addFromSource($this->getWriterPart('Drawing')->allDrawings($this->spreadSheet));

        $options = new Archive();
        $options->setEnableZip64(false);
        $options->setOutputStream($this->fileHandle);

        $this->zip = new ZipStream(null, $options);

        
        $this->addZipFile('[Content_Types].xml', $this->getWriterPart('ContentTypes')->writeContentTypes($this->spreadSheet, $this->includeCharts));

        
        if ($this->spreadSheet->hasMacros()) {
            $macrosCode = $this->spreadSheet->getMacrosCode();
            if ($macrosCode !== null) {
                
                $this->addZipFile('xl/vbaProject.bin', $macrosCode); 
                if ($this->spreadSheet->hasMacrosCertificate()) {
                    
                    
                    $this->addZipFile('xl/vbaProjectSignature.bin', $this->spreadSheet->getMacrosCertificate());
                    $this->addZipFile('xl/_rels/vbaProject.bin.rels', $this->getWriterPart('RelsVBA')->writeVBARelationships($this->spreadSheet));
                }
            }
        }
        
        if ($this->spreadSheet->hasRibbon()) {
            $tmpRibbonTarget = $this->spreadSheet->getRibbonXMLData('target');
            $this->addZipFile($tmpRibbonTarget, $this->spreadSheet->getRibbonXMLData('data'));
            if ($this->spreadSheet->hasRibbonBinObjects()) {
                $tmpRootPath = dirname($tmpRibbonTarget) . '/';
                $ribbonBinObjects = $this->spreadSheet->getRibbonBinObjects('data'); 
                foreach ($ribbonBinObjects as $aPath => $aContent) {
                    $this->addZipFile($tmpRootPath . $aPath, $aContent);
                }
                
                $this->addZipFile($tmpRootPath . '_rels/' . basename($tmpRibbonTarget) . '.rels', $this->getWriterPart('RelsRibbonObjects')->writeRibbonRelationships($this->spreadSheet));
            }
        }

        
        $this->addZipFile('_rels/.rels', $this->getWriterPart('Rels')->writeRelationships($this->spreadSheet));
        $this->addZipFile('xl/_rels/workbook.xml.rels', $this->getWriterPart('Rels')->writeWorkbookRelationships($this->spreadSheet));

        
        $this->addZipFile('docProps/app.xml', $this->getWriterPart('DocProps')->writeDocPropsApp($this->spreadSheet));
        $this->addZipFile('docProps/core.xml', $this->getWriterPart('DocProps')->writeDocPropsCore($this->spreadSheet));
        $customPropertiesPart = $this->getWriterPart('DocProps')->writeDocPropsCustom($this->spreadSheet);
        if ($customPropertiesPart !== null) {
            $this->addZipFile('docProps/custom.xml', $customPropertiesPart);
        }

        
        $this->addZipFile('xl/theme/theme1.xml', $this->getWriterPart('Theme')->writeTheme($this->spreadSheet));

        
        $this->addZipFile('xl/sharedStrings.xml', $this->getWriterPart('StringTable')->writeStringTable($this->stringTable));

        
        $this->addZipFile('xl/styles.xml', $this->getWriterPart('Style')->writeStyles($this->spreadSheet));

        
        $this->addZipFile('xl/workbook.xml', $this->getWriterPart('Workbook')->writeWorkbook($this->spreadSheet, $this->preCalculateFormulas));

        $chartCount = 0;
        
        for ($i = 0; $i < $this->spreadSheet->getSheetCount(); ++$i) {
            $this->addZipFile('xl/worksheets/sheet' . ($i + 1) . '.xml', $this->getWriterPart('Worksheet')->writeWorksheet($this->spreadSheet->getSheet($i), $this->stringTable, $this->includeCharts));
            if ($this->includeCharts) {
                $charts = $this->spreadSheet->getSheet($i)->getChartCollection();
                if (count($charts) > 0) {
                    foreach ($charts as $chart) {
                        $this->addZipFile('xl/charts/chart' . ($chartCount + 1) . '.xml', $this->getWriterPart('Chart')->writeChart($chart, $this->preCalculateFormulas));
                        ++$chartCount;
                    }
                }
            }
        }

        $chartRef1 = 0;
        
        for ($i = 0; $i < $this->spreadSheet->getSheetCount(); ++$i) {
            
            $this->addZipFile('xl/worksheets/_rels/sheet' . ($i + 1) . '.xml.rels', $this->getWriterPart('Rels')->writeWorksheetRelationships($this->spreadSheet->getSheet($i), ($i + 1), $this->includeCharts));

            
            $sheetCodeName = $this->spreadSheet->getSheet($i)->getCodeName();
            $unparsedLoadedData = $this->spreadSheet->getUnparsedLoadedData();
            if (isset($unparsedLoadedData['sheets'][$sheetCodeName]['ctrlProps'])) {
                foreach ($unparsedLoadedData['sheets'][$sheetCodeName]['ctrlProps'] as $ctrlProp) {
                    $this->addZipFile($ctrlProp['filePath'], $ctrlProp['content']);
                }
            }
            if (isset($unparsedLoadedData['sheets'][$sheetCodeName]['printerSettings'])) {
                foreach ($unparsedLoadedData['sheets'][$sheetCodeName]['printerSettings'] as $ctrlProp) {
                    $this->addZipFile($ctrlProp['filePath'], $ctrlProp['content']);
                }
            }

            $drawings = $this->spreadSheet->getSheet($i)->getDrawingCollection();
            $drawingCount = count($drawings);
            if ($this->includeCharts) {
                $chartCount = $this->spreadSheet->getSheet($i)->getChartCount();
            }

            
            if (($drawingCount > 0) || ($chartCount > 0)) {
                
                $this->addZipFile('xl/drawings/_rels/drawing' . ($i + 1) . '.xml.rels', $this->getWriterPart('Rels')->writeDrawingRelationships($this->spreadSheet->getSheet($i), $chartRef1, $this->includeCharts));

                
                $this->addZipFile('xl/drawings/drawing' . ($i + 1) . '.xml', $this->getWriterPart('Drawing')->writeDrawings($this->spreadSheet->getSheet($i), $this->includeCharts));
            } elseif (isset($unparsedLoadedData['sheets'][$sheetCodeName]['drawingAlternateContents'])) {
                
                $this->addZipFile('xl/drawings/drawing' . ($i + 1) . '.xml', $this->getWriterPart('Drawing')->writeDrawings($this->spreadSheet->getSheet($i), $this->includeCharts));
            }

            
            if (isset($unparsedLoadedData['sheets'][$sheetCodeName]['Drawings'])) {
                foreach ($unparsedLoadedData['sheets'][$sheetCodeName]['Drawings'] as $relId => $drawingXml) {
                    $drawingFile = array_search($relId, $unparsedLoadedData['sheets'][$sheetCodeName]['drawingOriginalIds']);
                    if ($drawingFile !== false) {
                        $drawingFile = ltrim($drawingFile, '.');
                        $this->addZipFile('xl' . $drawingFile, $drawingXml);
                    }
                }
            }

            
            if (count($this->spreadSheet->getSheet($i)->getComments()) > 0) {
                
                $this->addZipFile('xl/drawings/vmlDrawing' . ($i + 1) . '.vml', $this->getWriterPart('Comments')->writeVMLComments($this->spreadSheet->getSheet($i)));

                
                $this->addZipFile('xl/comments' . ($i + 1) . '.xml', $this->getWriterPart('Comments')->writeComments($this->spreadSheet->getSheet($i)));
            }

            
            if (isset($unparsedLoadedData['sheets'][$sheetCodeName]['vmlDrawings'])) {
                foreach ($unparsedLoadedData['sheets'][$sheetCodeName]['vmlDrawings'] as $vmlDrawing) {
                    $this->addZipFile($vmlDrawing['filePath'], $vmlDrawing['content']);
                }
            }

            
            if (count($this->spreadSheet->getSheet($i)->getHeaderFooter()->getImages()) > 0) {
                
                $this->addZipFile('xl/drawings/vmlDrawingHF' . ($i + 1) . '.vml', $this->getWriterPart('Drawing')->writeVMLHeaderFooterImages($this->spreadSheet->getSheet($i)));

                
                $this->addZipFile('xl/drawings/_rels/vmlDrawingHF' . ($i + 1) . '.vml.rels', $this->getWriterPart('Rels')->writeHeaderFooterDrawingRelationships($this->spreadSheet->getSheet($i)));

                
                foreach ($this->spreadSheet->getSheet($i)->getHeaderFooter()->getImages() as $image) {
                    $this->addZipFile('xl/media/' . $image->getIndexedFilename(), file_get_contents($image->getPath()));
                }
            }
        }

        
        for ($i = 0; $i < $this->getDrawingHashTable()->count(); ++$i) {
            if ($this->getDrawingHashTable()->getByIndex($i) instanceof WorksheetDrawing) {
                $imageContents = null;
                $imagePath = $this->getDrawingHashTable()->getByIndex($i)->getPath();
                if (strpos($imagePath, 'zip:
                    $imagePath = substr($imagePath, 6);
                    $imagePathSplitted = explode('#', $imagePath);

                    $imageZip = new ZipArchive();
                    $imageZip->open($imagePathSplitted[0]);
                    $imageContents = $imageZip->getFromName($imagePathSplitted[1]);
                    $imageZip->close();
                    unset($imageZip);
                } else {
                    $imageContents = file_get_contents($imagePath);
                }

                $this->addZipFile('xl/media/' . str_replace(' ', '_', $this->getDrawingHashTable()->getByIndex($i)->getIndexedFilename()), $imageContents);
            } elseif ($this->getDrawingHashTable()->getByIndex($i) instanceof MemoryDrawing) {
                ob_start();
                call_user_func(
                    $this->getDrawingHashTable()->getByIndex($i)->getRenderingFunction(),
                    $this->getDrawingHashTable()->getByIndex($i)->getImageResource()
                );
                $imageContents = ob_get_contents();
                ob_end_clean();

                $this->addZipFile('xl/media/' . str_replace(' ', '_', $this->getDrawingHashTable()->getByIndex($i)->getIndexedFilename()), $imageContents);
            }
        }

        Functions::setReturnDateType($saveDateReturnType);
        Calculation::getInstance($this->spreadSheet)->getDebugLog()->setWriteDebugLog($saveDebugLog);

        
        try {
            $this->zip->finish();
        } catch (OverflowException $e) {
            throw new WriterException('Could not close resource.');
        }

        $this->maybeCloseFileHandle();
    }

    
    public function getSpreadsheet()
    {
        return $this->spreadSheet;
    }

    
    public function setSpreadsheet(Spreadsheet $spreadsheet)
    {
        $this->spreadSheet = $spreadsheet;

        return $this;
    }

    
    public function getStringTable()
    {
        return $this->stringTable;
    }

    
    public function getStyleHashTable()
    {
        return $this->styleHashTable;
    }

    
    public function getStylesConditionalHashTable()
    {
        return $this->stylesConditionalHashTable;
    }

    
    public function getFillHashTable()
    {
        return $this->fillHashTable;
    }

    
    public function getFontHashTable()
    {
        return $this->fontHashTable;
    }

    
    public function getBordersHashTable()
    {
        return $this->bordersHashTable;
    }

    
    public function getNumFmtHashTable()
    {
        return $this->numFmtHashTable;
    }

    
    public function getDrawingHashTable()
    {
        return $this->drawingHashTable;
    }

    
    public function getOffice2003Compatibility()
    {
        return $this->office2003compatibility;
    }

    
    public function setOffice2003Compatibility($pValue)
    {
        $this->office2003compatibility = $pValue;

        return $this;
    }

    private $pathNames = [];

    private function addZipFile(string $path, string $content): void
    {
        if (!in_array($path, $this->pathNames)) {
            $this->pathNames[] = $path;
            $this->zip->addFile($path, $content);
        }
    }
}
