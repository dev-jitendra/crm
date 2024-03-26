<?php

namespace PhpOffice\PhpSpreadsheet\Reader;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\Hyperlink;
use PhpOffice\PhpSpreadsheet\DefinedName;
use PhpOffice\PhpSpreadsheet\Reader\Security\XmlScanner;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx\AutoFilter;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx\Chart;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx\ColumnAndRowAttributes;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx\ConditionalStyles;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx\DataValidations;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx\Hyperlinks;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx\PageSetup;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx\Properties as PropertyReader;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx\SheetViewOptions;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx\SheetViews;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx\Styles;
use PhpOffice\PhpSpreadsheet\ReferenceHelper;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Settings;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Shared\Drawing;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Shared\Font;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Borders;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Protection;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Worksheet\HeaderFooterDrawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use SimpleXMLElement;
use stdClass;
use Throwable;
use XMLReader;
use ZipArchive;

class Xlsx extends BaseReader
{
    
    private $referenceHelper;

    
    private static $theme = null;

    
    public function __construct()
    {
        parent::__construct();
        $this->referenceHelper = ReferenceHelper::getInstance();
        $this->securityScanner = XmlScanner::getInstance($this);
    }

    
    public function canRead($pFilename)
    {
        File::assertFile($pFilename);

        $result = false;
        $zip = new ZipArchive();

        if ($zip->open($pFilename) === true) {
            $workbookBasename = $this->getWorkbookBaseName($zip);
            $result = !empty($workbookBasename);

            $zip->close();
        }

        return $result;
    }

    
    public function listWorksheetNames($pFilename)
    {
        File::assertFile($pFilename);

        $worksheetNames = [];

        $zip = new ZipArchive();
        $zip->open($pFilename);

        
        
        $rels = simplexml_load_string(
            $this->securityScanner->scan($this->getFromZipArchive($zip, '_rels/.rels'))
        );
        foreach ($rels->Relationship as $rel) {
            switch ($rel['Type']) {
                case 'http:
                    
                    $xmlWorkbook = simplexml_load_string(
                        $this->securityScanner->scan($this->getFromZipArchive($zip, "{$rel['Target']}"))
                    );

                    if ($xmlWorkbook->sheets) {
                        foreach ($xmlWorkbook->sheets->sheet as $eleSheet) {
                            
                            $worksheetNames[] = (string) $eleSheet['name'];
                        }
                    }
            }
        }

        $zip->close();

        return $worksheetNames;
    }

    
    public function listWorksheetInfo($pFilename)
    {
        File::assertFile($pFilename);

        $worksheetInfo = [];

        $zip = new ZipArchive();
        $zip->open($pFilename);

        
        $rels = simplexml_load_string(
            $this->securityScanner->scan($this->getFromZipArchive($zip, '_rels/.rels')),
            'SimpleXMLElement',
            Settings::getLibXmlLoaderOptions()
        );
        foreach ($rels->Relationship as $rel) {
            if ($rel['Type'] == 'http:
                $dir = dirname($rel['Target']);

                
                $relsWorkbook = simplexml_load_string(
                    $this->securityScanner->scan(
                        $this->getFromZipArchive($zip, "$dir/_rels/" . basename($rel['Target']) . '.rels')
                    ),
                    'SimpleXMLElement',
                    Settings::getLibXmlLoaderOptions()
                );
                $relsWorkbook->registerXPathNamespace('rel', 'http:

                $worksheets = [];
                foreach ($relsWorkbook->Relationship as $ele) {
                    if ($ele['Type'] == 'http:
                        $worksheets[(string) $ele['Id']] = $ele['Target'];
                    }
                }

                
                $xmlWorkbook = simplexml_load_string(
                    $this->securityScanner->scan(
                        $this->getFromZipArchive($zip, "{$rel['Target']}")
                    ),
                    'SimpleXMLElement',
                    Settings::getLibXmlLoaderOptions()
                );
                if ($xmlWorkbook->sheets) {
                    $dir = dirname($rel['Target']);
                    
                    foreach ($xmlWorkbook->sheets->sheet as $eleSheet) {
                        $tmpInfo = [
                            'worksheetName' => (string) $eleSheet['name'],
                            'lastColumnLetter' => 'A',
                            'lastColumnIndex' => 0,
                            'totalRows' => 0,
                            'totalColumns' => 0,
                        ];

                        $fileWorksheet = $worksheets[(string) self::getArrayItem($eleSheet->attributes('http:

                        $xml = new XMLReader();
                        $xml->xml(
                            $this->securityScanner->scanFile(
                                'zip:
                            ),
                            null,
                            Settings::getLibXmlLoaderOptions()
                        );
                        $xml->setParserProperty(2, true);

                        $currCells = 0;
                        while ($xml->read()) {
                            if ($xml->name == 'row' && $xml->nodeType == XMLReader::ELEMENT) {
                                $row = $xml->getAttribute('r');
                                $tmpInfo['totalRows'] = $row;
                                $tmpInfo['totalColumns'] = max($tmpInfo['totalColumns'], $currCells);
                                $currCells = 0;
                            } elseif ($xml->name == 'c' && $xml->nodeType == XMLReader::ELEMENT) {
                                ++$currCells;
                            }
                        }
                        $tmpInfo['totalColumns'] = max($tmpInfo['totalColumns'], $currCells);
                        $xml->close();

                        $tmpInfo['lastColumnIndex'] = $tmpInfo['totalColumns'] - 1;
                        $tmpInfo['lastColumnLetter'] = Coordinate::stringFromColumnIndex($tmpInfo['lastColumnIndex'] + 1);

                        $worksheetInfo[] = $tmpInfo;
                    }
                }
            }
        }

        $zip->close();

        return $worksheetInfo;
    }

    private static function castToBoolean($c)
    {
        $value = isset($c->v) ? (string) $c->v : null;
        if ($value == '0') {
            return false;
        } elseif ($value == '1') {
            return true;
        }

        return (bool) $c->v;
    }

    private static function castToError($c)
    {
        return isset($c->v) ? (string) $c->v : null;
    }

    private static function castToString($c)
    {
        return isset($c->v) ? (string) $c->v : null;
    }

    private function castToFormula($c, $r, &$cellDataType, &$value, &$calculatedValue, &$sharedFormulas, $castBaseType): void
    {
        $cellDataType = 'f';
        $value = "={$c->f}";
        $calculatedValue = self::$castBaseType($c);

        
        if (isset($c->f['t']) && strtolower((string) $c->f['t']) == 'shared') {
            $instance = (string) $c->f['si'];

            if (!isset($sharedFormulas[(string) $c->f['si']])) {
                $sharedFormulas[$instance] = ['master' => $r, 'formula' => $value];
            } else {
                $master = Coordinate::coordinateFromString($sharedFormulas[$instance]['master']);
                $current = Coordinate::coordinateFromString($r);

                $difference = [0, 0];
                $difference[0] = Coordinate::columnIndexFromString($current[0]) - Coordinate::columnIndexFromString($master[0]);
                $difference[1] = $current[1] - $master[1];

                $value = $this->referenceHelper->updateFormulaReferences($sharedFormulas[$instance]['formula'], 'A1', $difference[0], $difference[1]);
            }
        }
    }

    
    private function getFromZipArchive(ZipArchive $archive, $fileName = '')
    {
        
        if (strpos($fileName, '
            $fileName = substr($fileName, strpos($fileName, '
        }
        $fileName = File::realpath($fileName);

        
        

        
        $contents = $archive->getFromName($fileName, 0, ZipArchive::FL_NOCASE);
        if ($contents === false) {
            $contents = $archive->getFromName(substr($fileName, 1), 0, ZipArchive::FL_NOCASE);
        }

        return $contents;
    }

    
    public function load($pFilename)
    {
        File::assertFile($pFilename);

        
        $excel = new Spreadsheet();
        $excel->removeSheetByIndex(0);
        if (!$this->readDataOnly) {
            $excel->removeCellStyleXfByIndex(0); 
            $excel->removeCellXfByIndex(0); 
        }
        $unparsedLoadedData = [];

        $zip = new ZipArchive();
        $zip->open($pFilename);

        
        
        $workbookBasename = $this->getWorkbookBaseName($zip);
        $wbRels = simplexml_load_string(
            $this->securityScanner->scan($this->getFromZipArchive($zip, "xl/_rels/${workbookBasename}.rels")),
            'SimpleXMLElement',
            Settings::getLibXmlLoaderOptions()
        );
        foreach ($wbRels->Relationship as $rel) {
            switch ($rel['Type']) {
                case 'http:
                    $themeOrderArray = ['lt1', 'dk1', 'lt2', 'dk2'];
                    $themeOrderAdditional = count($themeOrderArray);

                    $xmlTheme = simplexml_load_string(
                        $this->securityScanner->scan($this->getFromZipArchive($zip, "xl/{$rel['Target']}")),
                        'SimpleXMLElement',
                        Settings::getLibXmlLoaderOptions()
                    );
                    if (is_object($xmlTheme)) {
                        $xmlThemeName = $xmlTheme->attributes();
                        $xmlTheme = $xmlTheme->children('http:
                        $themeName = (string) $xmlThemeName['name'];

                        $colourScheme = $xmlTheme->themeElements->clrScheme->attributes();
                        $colourSchemeName = (string) $colourScheme['name'];
                        $colourScheme = $xmlTheme->themeElements->clrScheme->children('http:

                        $themeColours = [];
                        foreach ($colourScheme as $k => $xmlColour) {
                            $themePos = array_search($k, $themeOrderArray);
                            if ($themePos === false) {
                                $themePos = $themeOrderAdditional++;
                            }
                            if (isset($xmlColour->sysClr)) {
                                $xmlColourData = $xmlColour->sysClr->attributes();
                                $themeColours[$themePos] = $xmlColourData['lastClr'];
                            } elseif (isset($xmlColour->srgbClr)) {
                                $xmlColourData = $xmlColour->srgbClr->attributes();
                                $themeColours[$themePos] = $xmlColourData['val'];
                            }
                        }
                        self::$theme = new Xlsx\Theme($themeName, $colourSchemeName, $themeColours);
                    }

                    break;
            }
        }

        
        $rels = simplexml_load_string(
            $this->securityScanner->scan($this->getFromZipArchive($zip, '_rels/.rels')),
            'SimpleXMLElement',
            Settings::getLibXmlLoaderOptions()
        );

        $propertyReader = new PropertyReader($this->securityScanner, $excel->getProperties());
        foreach ($rels->Relationship as $rel) {
            switch ($rel['Type']) {
                case 'http:
                    $propertyReader->readCoreProperties($this->getFromZipArchive($zip, "{$rel['Target']}"));

                    break;
                case 'http:
                    $propertyReader->readExtendedProperties($this->getFromZipArchive($zip, "{$rel['Target']}"));

                    break;
                case 'http:
                    $propertyReader->readCustomProperties($this->getFromZipArchive($zip, "{$rel['Target']}"));

                    break;
                
                case 'http:
                    $customUI = $rel['Target'];
                    if ($customUI !== null) {
                        $this->readRibbon($excel, $customUI, $zip);
                    }

                    break;
                case 'http:
                    $dir = dirname($rel['Target']);
                    
                    $relsWorkbook = simplexml_load_string(
                        $this->securityScanner->scan($this->getFromZipArchive($zip, "$dir/_rels/" . basename($rel['Target']) . '.rels')),
                        'SimpleXMLElement',
                        Settings::getLibXmlLoaderOptions()
                    );
                    $relsWorkbook->registerXPathNamespace('rel', 'http:

                    $sharedStrings = [];
                    $xpath = self::getArrayItem($relsWorkbook->xpath("rel:Relationship[@Type='http:
                    if ($xpath) {
                        
                        $xmlStrings = simplexml_load_string(
                            $this->securityScanner->scan($this->getFromZipArchive($zip, "$dir/$xpath[Target]")),
                            'SimpleXMLElement',
                            Settings::getLibXmlLoaderOptions()
                        );
                        if (isset($xmlStrings, $xmlStrings->si)) {
                            foreach ($xmlStrings->si as $val) {
                                if (isset($val->t)) {
                                    $sharedStrings[] = StringHelper::controlCharacterOOXML2PHP((string) $val->t);
                                } elseif (isset($val->r)) {
                                    $sharedStrings[] = $this->parseRichText($val);
                                }
                            }
                        }
                    }

                    $worksheets = [];
                    $macros = $customUI = null;
                    foreach ($relsWorkbook->Relationship as $ele) {
                        switch ($ele['Type']) {
                            case 'http:
                                $worksheets[(string) $ele['Id']] = $ele['Target'];

                                break;
                            
                            case 'http:
                                $macros = $ele['Target'];

                                break;
                        }
                    }

                    if ($macros !== null) {
                        $macrosCode = $this->getFromZipArchive($zip, 'xl/vbaProject.bin'); 
                        if ($macrosCode !== false) {
                            $excel->setMacrosCode($macrosCode);
                            $excel->setHasMacros(true);
                            
                            $Certificate = $this->getFromZipArchive($zip, 'xl/vbaProjectSignature.bin');
                            if ($Certificate !== false) {
                                $excel->setMacrosCertificate($Certificate);
                            }
                        }
                    }

                    $xpath = self::getArrayItem($relsWorkbook->xpath("rel:Relationship[@Type='http:
                    
                    $xmlStyles = simplexml_load_string(
                        $this->securityScanner->scan($this->getFromZipArchive($zip, "$dir/$xpath[Target]")),
                        'SimpleXMLElement',
                        Settings::getLibXmlLoaderOptions()
                    );

                    $styles = [];
                    $cellStyles = [];
                    $numFmts = null;
                    if ($xmlStyles && $xmlStyles->numFmts[0]) {
                        $numFmts = $xmlStyles->numFmts[0];
                    }
                    if (isset($numFmts) && ($numFmts !== null)) {
                        $numFmts->registerXPathNamespace('sml', 'http:
                    }
                    if (!$this->readDataOnly && $xmlStyles) {
                        foreach ($xmlStyles->cellXfs->xf as $xf) {
                            $numFmt = NumberFormat::FORMAT_GENERAL;

                            if ($xf['numFmtId']) {
                                if (isset($numFmts)) {
                                    $tmpNumFmt = self::getArrayItem($numFmts->xpath("sml:numFmt[@numFmtId=$xf[numFmtId]]"));

                                    if (isset($tmpNumFmt['formatCode'])) {
                                        $numFmt = (string) $tmpNumFmt['formatCode'];
                                    }
                                }

                                
                                
                                
                                if (
                                    (int) $xf['numFmtId'] < 164 &&
                                    NumberFormat::builtInFormatCode((int) $xf['numFmtId']) !== ''
                                ) {
                                    $numFmt = NumberFormat::builtInFormatCode((int) $xf['numFmtId']);
                                }
                            }
                            $quotePrefix = false;
                            if (isset($xf['quotePrefix'])) {
                                $quotePrefix = (bool) $xf['quotePrefix'];
                            }

                            $style = (object) [
                                'numFmt' => $numFmt,
                                'font' => $xmlStyles->fonts->font[(int) ($xf['fontId'])],
                                'fill' => $xmlStyles->fills->fill[(int) ($xf['fillId'])],
                                'border' => $xmlStyles->borders->border[(int) ($xf['borderId'])],
                                'alignment' => $xf->alignment,
                                'protection' => $xf->protection,
                                'quotePrefix' => $quotePrefix,
                            ];
                            $styles[] = $style;

                            
                            $objStyle = new Style();
                            self::readStyle($objStyle, $style);
                            $excel->addCellXf($objStyle);
                        }

                        foreach (isset($xmlStyles->cellStyleXfs->xf) ? $xmlStyles->cellStyleXfs->xf : [] as $xf) {
                            $numFmt = NumberFormat::FORMAT_GENERAL;
                            if ($numFmts && $xf['numFmtId']) {
                                $tmpNumFmt = self::getArrayItem($numFmts->xpath("sml:numFmt[@numFmtId=$xf[numFmtId]]"));
                                if (isset($tmpNumFmt['formatCode'])) {
                                    $numFmt = (string) $tmpNumFmt['formatCode'];
                                } elseif ((int) $xf['numFmtId'] < 165) {
                                    $numFmt = NumberFormat::builtInFormatCode((int) $xf['numFmtId']);
                                }
                            }

                            $cellStyle = (object) [
                                'numFmt' => $numFmt,
                                'font' => $xmlStyles->fonts->font[(int) ($xf['fontId'])],
                                'fill' => $xmlStyles->fills->fill[(int) ($xf['fillId'])],
                                'border' => $xmlStyles->borders->border[(int) ($xf['borderId'])],
                                'alignment' => $xf->alignment,
                                'protection' => $xf->protection,
                                'quotePrefix' => $quotePrefix,
                            ];
                            $cellStyles[] = $cellStyle;

                            
                            $objStyle = new Style();
                            self::readStyle($objStyle, $cellStyle);
                            $excel->addCellStyleXf($objStyle);
                        }
                    }

                    $styleReader = new Styles($xmlStyles);
                    $styleReader->setStyleBaseData(self::$theme, $styles, $cellStyles);
                    $dxfs = $styleReader->dxfs($this->readDataOnly);
                    $styles = $styleReader->styles();

                    
                    $xmlWorkbook = simplexml_load_string(
                        $this->securityScanner->scan($this->getFromZipArchive($zip, "{$rel['Target']}")),
                        'SimpleXMLElement',
                        Settings::getLibXmlLoaderOptions()
                    );

                    
                    if ($xmlWorkbook->workbookPr) {
                        Date::setExcelCalendar(Date::CALENDAR_WINDOWS_1900);
                        if (isset($xmlWorkbook->workbookPr['date1904'])) {
                            if (self::boolean((string) $xmlWorkbook->workbookPr['date1904'])) {
                                Date::setExcelCalendar(Date::CALENDAR_MAC_1904);
                            }
                        }
                    }

                    
                    $this->readProtection($excel, $xmlWorkbook);

                    $sheetId = 0; 
                    $oldSheetId = -1; 
                    $countSkippedSheets = 0; 
                    $mapSheetId = []; 

                    $charts = $chartDetails = [];

                    if ($xmlWorkbook->sheets) {
                        
                        foreach ($xmlWorkbook->sheets->sheet as $eleSheet) {
                            ++$oldSheetId;

                            
                            if (isset($this->loadSheetsOnly) && !in_array((string) $eleSheet['name'], $this->loadSheetsOnly)) {
                                ++$countSkippedSheets;
                                $mapSheetId[$oldSheetId] = null;

                                continue;
                            }

                            
                            
                            $mapSheetId[$oldSheetId] = $oldSheetId - $countSkippedSheets;

                            
                            $docSheet = $excel->createSheet();
                            
                            
                            
                            
                            $docSheet->setTitle((string) $eleSheet['name'], false, false);
                            $fileWorksheet = $worksheets[(string) self::getArrayItem($eleSheet->attributes('http:
                            
                            $xmlSheet = simplexml_load_string(
                                $this->securityScanner->scan($this->getFromZipArchive($zip, "$dir/$fileWorksheet")),
                                'SimpleXMLElement',
                                Settings::getLibXmlLoaderOptions()
                            );

                            $sharedFormulas = [];

                            if (isset($eleSheet['state']) && (string) $eleSheet['state'] != '') {
                                $docSheet->setSheetState((string) $eleSheet['state']);
                            }

                            if ($xmlSheet) {
                                if (isset($xmlSheet->sheetViews, $xmlSheet->sheetViews->sheetView)) {
                                    $sheetViews = new SheetViews($xmlSheet->sheetViews->sheetView, $docSheet);
                                    $sheetViews->load();
                                }

                                $sheetViewOptions = new SheetViewOptions($docSheet, $xmlSheet);
                                $sheetViewOptions->load($this->getReadDataOnly());

                                (new ColumnAndRowAttributes($docSheet, $xmlSheet))
                                    ->load($this->getReadFilter(), $this->getReadDataOnly());
                            }

                            if ($xmlSheet && $xmlSheet->sheetData && $xmlSheet->sheetData->row) {
                                $cIndex = 1; 
                                foreach ($xmlSheet->sheetData->row as $row) {
                                    $rowIndex = 1;
                                    foreach ($row->c as $c) {
                                        $r = (string) $c['r'];
                                        if ($r == '') {
                                            $r = Coordinate::stringFromColumnIndex($rowIndex) . $cIndex;
                                        }
                                        $cellDataType = (string) $c['t'];
                                        $value = null;
                                        $calculatedValue = null;

                                        
                                        if ($this->getReadFilter() !== null) {
                                            $coordinates = Coordinate::coordinateFromString($r);

                                            if (!$this->getReadFilter()->readCell($coordinates[0], (int) $coordinates[1], $docSheet->getTitle())) {
                                                if (isset($c->f)) {
                                                    $this->castToFormula($c, $r, $cellDataType, $value, $calculatedValue, $sharedFormulas, 'castToError');
                                                }
                                                ++$rowIndex;

                                                continue;
                                            }
                                        }

                                        
                                        switch ($cellDataType) {
                                            case 's':
                                                if ((string) $c->v != '') {
                                                    $value = $sharedStrings[(int) ($c->v)];

                                                    if ($value instanceof RichText) {
                                                        $value = clone $value;
                                                    }
                                                } else {
                                                    $value = '';
                                                }

                                                break;
                                            case 'b':
                                                if (!isset($c->f)) {
                                                    $value = self::castToBoolean($c);
                                                } else {
                                                    
                                                    $this->castToFormula($c, $r, $cellDataType, $value, $calculatedValue, $sharedFormulas, 'castToBoolean');
                                                    if (isset($c->f['t'])) {
                                                        $att = $c->f;
                                                        $docSheet->getCell($r)->setFormulaAttributes($att);
                                                    }
                                                }

                                                break;
                                            case 'inlineStr':
                                                if (isset($c->f)) {
                                                    $this->castToFormula($c, $r, $cellDataType, $value, $calculatedValue, $sharedFormulas, 'castToError');
                                                } else {
                                                    $value = $this->parseRichText($c->is);
                                                }

                                                break;
                                            case 'e':
                                                if (!isset($c->f)) {
                                                    $value = self::castToError($c);
                                                } else {
                                                    
                                                    $this->castToFormula($c, $r, $cellDataType, $value, $calculatedValue, $sharedFormulas, 'castToError');
                                                }

                                                break;
                                            default:
                                                if (!isset($c->f)) {
                                                    $value = self::castToString($c);
                                                } else {
                                                    
                                                    $this->castToFormula($c, $r, $cellDataType, $value, $calculatedValue, $sharedFormulas, 'castToString');
                                                }

                                                break;
                                        }

                                        
                                        if ($this->readEmptyCells || ($value !== null && $value !== '')) {
                                            
                                            if ($value instanceof RichText && $this->readDataOnly) {
                                                $value = $value->getPlainText();
                                            }

                                            $cell = $docSheet->getCell($r);
                                            
                                            if ($cellDataType != '') {
                                                $cell->setValueExplicit($value, $cellDataType);
                                            } else {
                                                $cell->setValue($value);
                                            }
                                            if ($calculatedValue !== null) {
                                                $cell->setCalculatedValue($calculatedValue);
                                            }

                                            
                                            if ($c['s'] && !$this->readDataOnly) {
                                                
                                                $cell->setXfIndex(isset($styles[(int) ($c['s'])]) ?
                                                    (int) ($c['s']) : 0);
                                            }
                                        }
                                        ++$rowIndex;
                                    }
                                    ++$cIndex;
                                }
                            }

                            if (!$this->readDataOnly && $xmlSheet && $xmlSheet->conditionalFormatting) {
                                (new ConditionalStyles($docSheet, $xmlSheet, $dxfs))->load();
                            }

                            $aKeys = ['sheet', 'objects', 'scenarios', 'formatCells', 'formatColumns', 'formatRows', 'insertColumns', 'insertRows', 'insertHyperlinks', 'deleteColumns', 'deleteRows', 'selectLockedCells', 'sort', 'autoFilter', 'pivotTables', 'selectUnlockedCells'];
                            if (!$this->readDataOnly && $xmlSheet && $xmlSheet->sheetProtection) {
                                foreach ($aKeys as $key) {
                                    $method = 'set' . ucfirst($key);
                                    $docSheet->getProtection()->$method(self::boolean((string) $xmlSheet->sheetProtection[$key]));
                                }
                            }

                            if ($xmlSheet) {
                                $this->readSheetProtection($docSheet, $xmlSheet);
                            }

                            if ($xmlSheet && $xmlSheet->autoFilter && !$this->readDataOnly) {
                                (new AutoFilter($docSheet, $xmlSheet))->load();
                            }

                            if ($xmlSheet && $xmlSheet->mergeCells && $xmlSheet->mergeCells->mergeCell && !$this->readDataOnly) {
                                foreach ($xmlSheet->mergeCells->mergeCell as $mergeCell) {
                                    $mergeRef = (string) $mergeCell['ref'];
                                    if (strpos($mergeRef, ':') !== false) {
                                        $docSheet->mergeCells((string) $mergeCell['ref']);
                                    }
                                }
                            }

                            if ($xmlSheet && !$this->readDataOnly) {
                                $unparsedLoadedData = (new PageSetup($docSheet, $xmlSheet))->load($unparsedLoadedData);
                            }

                            if ($xmlSheet && $xmlSheet->dataValidations && !$this->readDataOnly) {
                                (new DataValidations($docSheet, $xmlSheet))->load();
                            }

                            
                            if ($xmlSheet && !$this->readDataOnly) {
                                $mc = $xmlSheet->children('http:
                                if ($mc->AlternateContent) {
                                    foreach ($mc->AlternateContent as $alternateContent) {
                                        $unparsedLoadedData['sheets'][$docSheet->getCodeName()]['AlternateContents'][] = $alternateContent->asXML();
                                    }
                                }
                            }

                            
                            if (!$this->readDataOnly) {
                                $hyperlinkReader = new Hyperlinks($docSheet);
                                
                                $relationsFileName = dirname("$dir/$fileWorksheet") . '/_rels/' . basename($fileWorksheet) . '.rels';
                                if ($zip->locateName($relationsFileName)) {
                                    
                                    $relsWorksheet = simplexml_load_string(
                                        $this->securityScanner->scan(
                                            $this->getFromZipArchive($zip, $relationsFileName)
                                        ),
                                        'SimpleXMLElement',
                                        Settings::getLibXmlLoaderOptions()
                                    );
                                    $hyperlinkReader->readHyperlinks($relsWorksheet);
                                }

                                
                                if ($xmlSheet && $xmlSheet->hyperlinks) {
                                    $hyperlinkReader->setHyperlinks($xmlSheet->hyperlinks);
                                }
                            }

                            
                            $comments = [];
                            $vmlComments = [];
                            if (!$this->readDataOnly) {
                                
                                if ($zip->locateName(dirname("$dir/$fileWorksheet") . '/_rels/' . basename($fileWorksheet) . '.rels')) {
                                    
                                    $relsWorksheet = simplexml_load_string(
                                        $this->securityScanner->scan(
                                            $this->getFromZipArchive($zip, dirname("$dir/$fileWorksheet") . '/_rels/' . basename($fileWorksheet) . '.rels')
                                        ),
                                        'SimpleXMLElement',
                                        Settings::getLibXmlLoaderOptions()
                                    );
                                    foreach ($relsWorksheet->Relationship as $ele) {
                                        if ($ele['Type'] == 'http:
                                            $comments[(string) $ele['Id']] = (string) $ele['Target'];
                                        }
                                        if ($ele['Type'] == 'http:
                                            $vmlComments[(string) $ele['Id']] = (string) $ele['Target'];
                                        }
                                    }
                                }

                                
                                foreach ($comments as $relName => $relPath) {
                                    
                                    $relPath = File::realpath(dirname("$dir/$fileWorksheet") . '/' . $relPath);
                                    $commentsFile = simplexml_load_string(
                                        $this->securityScanner->scan($this->getFromZipArchive($zip, $relPath)),
                                        'SimpleXMLElement',
                                        Settings::getLibXmlLoaderOptions()
                                    );

                                    
                                    $authors = [];

                                    
                                    foreach ($commentsFile->authors->author as $author) {
                                        $authors[] = (string) $author;
                                    }

                                    
                                    foreach ($commentsFile->commentList->comment as $comment) {
                                        if (!empty($comment['authorId'])) {
                                            $docSheet->getComment((string) $comment['ref'])->setAuthor($authors[(string) $comment['authorId']]);
                                        }
                                        $docSheet->getComment((string) $comment['ref'])->setText($this->parseRichText($comment->text));
                                    }
                                }

                                
                                $unparsedVmlDrawings = $vmlComments;

                                
                                foreach ($vmlComments as $relName => $relPath) {
                                    
                                    $relPath = File::realpath(dirname("$dir/$fileWorksheet") . '/' . $relPath);

                                    try {
                                        $vmlCommentsFile = simplexml_load_string(
                                            $this->securityScanner->scan($this->getFromZipArchive($zip, $relPath)),
                                            'SimpleXMLElement',
                                            Settings::getLibXmlLoaderOptions()
                                        );
                                        $vmlCommentsFile->registerXPathNamespace('v', 'urn:schemas-microsoft-com:vml');
                                    } catch (Throwable $ex) {
                                        
                                        continue;
                                    }

                                    $shapes = $vmlCommentsFile->xpath('
                                    foreach ($shapes as $shape) {
                                        $shape->registerXPathNamespace('v', 'urn:schemas-microsoft-com:vml');

                                        if (isset($shape['style'])) {
                                            $style = (string) $shape['style'];
                                            $fillColor = strtoupper(substr((string) $shape['fillcolor'], 1));
                                            $column = null;
                                            $row = null;

                                            $clientData = $shape->xpath('.
                                            if (is_array($clientData) && !empty($clientData)) {
                                                $clientData = $clientData[0];

                                                if (isset($clientData['ObjectType']) && (string) $clientData['ObjectType'] == 'Note') {
                                                    $temp = $clientData->xpath('.
                                                    if (is_array($temp)) {
                                                        $row = $temp[0];
                                                    }

                                                    $temp = $clientData->xpath('.
                                                    if (is_array($temp)) {
                                                        $column = $temp[0];
                                                    }
                                                }
                                            }

                                            if (($column !== null) && ($row !== null)) {
                                                
                                                $comment = $docSheet->getCommentByColumnAndRow($column + 1, $row + 1);
                                                $comment->getFillColor()->setRGB($fillColor);

                                                
                                                $styleArray = explode(';', str_replace(' ', '', $style));
                                                foreach ($styleArray as $stylePair) {
                                                    $stylePair = explode(':', $stylePair);

                                                    if ($stylePair[0] == 'margin-left') {
                                                        $comment->setMarginLeft($stylePair[1]);
                                                    }
                                                    if ($stylePair[0] == 'margin-top') {
                                                        $comment->setMarginTop($stylePair[1]);
                                                    }
                                                    if ($stylePair[0] == 'width') {
                                                        $comment->setWidth($stylePair[1]);
                                                    }
                                                    if ($stylePair[0] == 'height') {
                                                        $comment->setHeight($stylePair[1]);
                                                    }
                                                    if ($stylePair[0] == 'visibility') {
                                                        $comment->setVisible($stylePair[1] == 'visible');
                                                    }
                                                }

                                                unset($unparsedVmlDrawings[$relName]);
                                            }
                                        }
                                    }
                                }

                                
                                if ($unparsedVmlDrawings) {
                                    foreach ($unparsedVmlDrawings as $rId => $relPath) {
                                        $rId = substr($rId, 3); 
                                        $unparsedVmlDrawing = &$unparsedLoadedData['sheets'][$docSheet->getCodeName()]['vmlDrawings'];
                                        $unparsedVmlDrawing[$rId] = [];
                                        $unparsedVmlDrawing[$rId]['filePath'] = self::dirAdd("$dir/$fileWorksheet", $relPath);
                                        $unparsedVmlDrawing[$rId]['relFilePath'] = $relPath;
                                        $unparsedVmlDrawing[$rId]['content'] = $this->securityScanner->scan($this->getFromZipArchive($zip, $unparsedVmlDrawing[$rId]['filePath']));
                                        unset($unparsedVmlDrawing);
                                    }
                                }

                                
                                if ($xmlSheet && $xmlSheet->legacyDrawingHF && !$this->readDataOnly) {
                                    if ($zip->locateName(dirname("$dir/$fileWorksheet") . '/_rels/' . basename($fileWorksheet) . '.rels')) {
                                        
                                        $relsWorksheet = simplexml_load_string(
                                            $this->securityScanner->scan(
                                                $this->getFromZipArchive($zip, dirname("$dir/$fileWorksheet") . '/_rels/' . basename($fileWorksheet) . '.rels')
                                            ),
                                            'SimpleXMLElement',
                                            Settings::getLibXmlLoaderOptions()
                                        );
                                        $vmlRelationship = '';

                                        foreach ($relsWorksheet->Relationship as $ele) {
                                            if ($ele['Type'] == 'http:
                                                $vmlRelationship = self::dirAdd("$dir/$fileWorksheet", $ele['Target']);
                                            }
                                        }

                                        if ($vmlRelationship != '') {
                                            
                                            
                                            $relsVML = simplexml_load_string(
                                                $this->securityScanner->scan(
                                                    $this->getFromZipArchive($zip, dirname($vmlRelationship) . '/_rels/' . basename($vmlRelationship) . '.rels')
                                                ),
                                                'SimpleXMLElement',
                                                Settings::getLibXmlLoaderOptions()
                                            );
                                            $drawings = [];
                                            if (isset($relsVML->Relationship)) {
                                                foreach ($relsVML->Relationship as $ele) {
                                                    if ($ele['Type'] == 'http:
                                                        $drawings[(string) $ele['Id']] = self::dirAdd($vmlRelationship, $ele['Target']);
                                                    }
                                                }
                                            }
                                            
                                            $vmlDrawing = simplexml_load_string(
                                                $this->securityScanner->scan($this->getFromZipArchive($zip, $vmlRelationship)),
                                                'SimpleXMLElement',
                                                Settings::getLibXmlLoaderOptions()
                                            );
                                            $vmlDrawing->registerXPathNamespace('v', 'urn:schemas-microsoft-com:vml');

                                            $hfImages = [];

                                            $shapes = $vmlDrawing->xpath('
                                            foreach ($shapes as $idx => $shape) {
                                                $shape->registerXPathNamespace('v', 'urn:schemas-microsoft-com:vml');
                                                $imageData = $shape->xpath('

                                                if (!$imageData) {
                                                    continue;
                                                }

                                                $imageData = $imageData[$idx];

                                                $imageData = $imageData->attributes('urn:schemas-microsoft-com:office:office');
                                                $style = self::toCSSArray((string) $shape['style']);

                                                $hfImages[(string) $shape['id']] = new HeaderFooterDrawing();
                                                if (isset($imageData['title'])) {
                                                    $hfImages[(string) $shape['id']]->setName((string) $imageData['title']);
                                                }

                                                $hfImages[(string) $shape['id']]->setPath('zip:
                                                $hfImages[(string) $shape['id']]->setResizeProportional(false);
                                                $hfImages[(string) $shape['id']]->setWidth($style['width']);
                                                $hfImages[(string) $shape['id']]->setHeight($style['height']);
                                                if (isset($style['margin-left'])) {
                                                    $hfImages[(string) $shape['id']]->setOffsetX($style['margin-left']);
                                                }
                                                $hfImages[(string) $shape['id']]->setOffsetY($style['margin-top']);
                                                $hfImages[(string) $shape['id']]->setResizeProportional(true);
                                            }

                                            $docSheet->getHeaderFooter()->setImages($hfImages);
                                        }
                                    }
                                }
                            }

                            
                            if ($zip->locateName(dirname("$dir/$fileWorksheet") . '/_rels/' . basename($fileWorksheet) . '.rels')) {
                                
                                $relsWorksheet = simplexml_load_string(
                                    $this->securityScanner->scan(
                                        $this->getFromZipArchive($zip, dirname("$dir/$fileWorksheet") . '/_rels/' . basename($fileWorksheet) . '.rels')
                                    ),
                                    'SimpleXMLElement',
                                    Settings::getLibXmlLoaderOptions()
                                );
                                $drawings = [];
                                foreach ($relsWorksheet->Relationship as $ele) {
                                    if ($ele['Type'] == 'http:
                                        $drawings[(string) $ele['Id']] = self::dirAdd("$dir/$fileWorksheet", $ele['Target']);
                                    }
                                }
                                if ($xmlSheet->drawing && !$this->readDataOnly) {
                                    $unparsedDrawings = [];
                                    foreach ($xmlSheet->drawing as $drawing) {
                                        $drawingRelId = (string) self::getArrayItem($drawing->attributes('http:
                                        $fileDrawing = $drawings[$drawingRelId];
                                        
                                        $relsDrawing = simplexml_load_string(
                                            $this->securityScanner->scan(
                                                $this->getFromZipArchive($zip, dirname($fileDrawing) . '/_rels/' . basename($fileDrawing) . '.rels')
                                            ),
                                            'SimpleXMLElement',
                                            Settings::getLibXmlLoaderOptions()
                                        );
                                        $images = [];
                                        $hyperlinks = [];
                                        if ($relsDrawing && $relsDrawing->Relationship) {
                                            foreach ($relsDrawing->Relationship as $ele) {
                                                if ($ele['Type'] == 'http:
                                                    $hyperlinks[(string) $ele['Id']] = (string) $ele['Target'];
                                                }
                                                if ($ele['Type'] == 'http:
                                                    $images[(string) $ele['Id']] = self::dirAdd($fileDrawing, $ele['Target']);
                                                } elseif ($ele['Type'] == 'http:
                                                    if ($this->includeCharts) {
                                                        $charts[self::dirAdd($fileDrawing, $ele['Target'])] = [
                                                            'id' => (string) $ele['Id'],
                                                            'sheet' => $docSheet->getTitle(),
                                                        ];
                                                    }
                                                }
                                            }
                                        }
                                        $xmlDrawing = simplexml_load_string(
                                            $this->securityScanner->scan($this->getFromZipArchive($zip, $fileDrawing)),
                                            'SimpleXMLElement',
                                            Settings::getLibXmlLoaderOptions()
                                        );
                                        $xmlDrawingChildren = $xmlDrawing->children('http:

                                        if ($xmlDrawingChildren->oneCellAnchor) {
                                            foreach ($xmlDrawingChildren->oneCellAnchor as $oneCellAnchor) {
                                                if ($oneCellAnchor->pic->blipFill) {
                                                    
                                                    $blip = $oneCellAnchor->pic->blipFill->children('http:
                                                    
                                                    $xfrm = $oneCellAnchor->pic->spPr->children('http:
                                                    
                                                    $outerShdw = $oneCellAnchor->pic->spPr->children('http:
                                                    
                                                    $hlinkClick = $oneCellAnchor->pic->nvPicPr->cNvPr->children('http:

                                                    $objDrawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                                                    $objDrawing->setName((string) self::getArrayItem($oneCellAnchor->pic->nvPicPr->cNvPr->attributes(), 'name'));
                                                    $objDrawing->setDescription((string) self::getArrayItem($oneCellAnchor->pic->nvPicPr->cNvPr->attributes(), 'descr'));
                                                    $objDrawing->setPath(
                                                        'zip:
                                                        $images[(string) self::getArrayItem(
                                                            $blip->attributes('http:
                                                            'embed'
                                                        )],
                                                        false
                                                    );
                                                    $objDrawing->setCoordinates(Coordinate::stringFromColumnIndex(((string) $oneCellAnchor->from->col) + 1) . ($oneCellAnchor->from->row + 1));
                                                    $objDrawing->setOffsetX(Drawing::EMUToPixels($oneCellAnchor->from->colOff));
                                                    $objDrawing->setOffsetY(Drawing::EMUToPixels($oneCellAnchor->from->rowOff));
                                                    $objDrawing->setResizeProportional(false);
                                                    $objDrawing->setWidth(Drawing::EMUToPixels(self::getArrayItem($oneCellAnchor->ext->attributes(), 'cx')));
                                                    $objDrawing->setHeight(Drawing::EMUToPixels(self::getArrayItem($oneCellAnchor->ext->attributes(), 'cy')));
                                                    if ($xfrm) {
                                                        $objDrawing->setRotation(Drawing::angleToDegrees(self::getArrayItem($xfrm->attributes(), 'rot')));
                                                    }
                                                    if ($outerShdw) {
                                                        $shadow = $objDrawing->getShadow();
                                                        $shadow->setVisible(true);
                                                        $shadow->setBlurRadius(Drawing::EMUToPixels(self::getArrayItem($outerShdw->attributes(), 'blurRad')));
                                                        $shadow->setDistance(Drawing::EMUToPixels(self::getArrayItem($outerShdw->attributes(), 'dist')));
                                                        $shadow->setDirection(Drawing::angleToDegrees(self::getArrayItem($outerShdw->attributes(), 'dir')));
                                                        $shadow->setAlignment((string) self::getArrayItem($outerShdw->attributes(), 'algn'));
                                                        $clr = isset($outerShdw->srgbClr) ? $outerShdw->srgbClr : $outerShdw->prstClr;
                                                        $shadow->getColor()->setRGB(self::getArrayItem($clr->attributes(), 'val'));
                                                        $shadow->setAlpha(self::getArrayItem($clr->alpha->attributes(), 'val') / 1000);
                                                    }

                                                    $this->readHyperLinkDrawing($objDrawing, $oneCellAnchor, $hyperlinks);

                                                    $objDrawing->setWorksheet($docSheet);
                                                } else {
                                                    
                                                    $coordinates = Coordinate::stringFromColumnIndex(((string) $oneCellAnchor->from->col) + 1) . ($oneCellAnchor->from->row + 1);
                                                    $offsetX = Drawing::EMUToPixels($oneCellAnchor->from->colOff);
                                                    $offsetY = Drawing::EMUToPixels($oneCellAnchor->from->rowOff);
                                                    $width = Drawing::EMUToPixels(self::getArrayItem($oneCellAnchor->ext->attributes(), 'cx'));
                                                    $height = Drawing::EMUToPixels(self::getArrayItem($oneCellAnchor->ext->attributes(), 'cy'));
                                                }
                                            }
                                        }
                                        if ($xmlDrawingChildren->twoCellAnchor) {
                                            foreach ($xmlDrawingChildren->twoCellAnchor as $twoCellAnchor) {
                                                if ($twoCellAnchor->pic->blipFill) {
                                                    $blip = $twoCellAnchor->pic->blipFill->children('http:
                                                    $xfrm = $twoCellAnchor->pic->spPr->children('http:
                                                    $outerShdw = $twoCellAnchor->pic->spPr->children('http:
                                                    $hlinkClick = $twoCellAnchor->pic->nvPicPr->cNvPr->children('http:
                                                    $objDrawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                                                    $objDrawing->setName((string) self::getArrayItem($twoCellAnchor->pic->nvPicPr->cNvPr->attributes(), 'name'));
                                                    $objDrawing->setDescription((string) self::getArrayItem($twoCellAnchor->pic->nvPicPr->cNvPr->attributes(), 'descr'));
                                                    $objDrawing->setPath(
                                                        'zip:
                                                        $images[(string) self::getArrayItem(
                                                            $blip->attributes('http:
                                                            'embed'
                                                        )],
                                                        false
                                                    );
                                                    $objDrawing->setCoordinates(Coordinate::stringFromColumnIndex(((string) $twoCellAnchor->from->col) + 1) . ($twoCellAnchor->from->row + 1));
                                                    $objDrawing->setOffsetX(Drawing::EMUToPixels($twoCellAnchor->from->colOff));
                                                    $objDrawing->setOffsetY(Drawing::EMUToPixels($twoCellAnchor->from->rowOff));
                                                    $objDrawing->setResizeProportional(false);

                                                    if ($xfrm) {
                                                        $objDrawing->setWidth(Drawing::EMUToPixels(self::getArrayItem($xfrm->ext->attributes(), 'cx')));
                                                        $objDrawing->setHeight(Drawing::EMUToPixels(self::getArrayItem($xfrm->ext->attributes(), 'cy')));
                                                        $objDrawing->setRotation(Drawing::angleToDegrees(self::getArrayItem($xfrm->attributes(), 'rot')));
                                                    }
                                                    if ($outerShdw) {
                                                        $shadow = $objDrawing->getShadow();
                                                        $shadow->setVisible(true);
                                                        $shadow->setBlurRadius(Drawing::EMUToPixels(self::getArrayItem($outerShdw->attributes(), 'blurRad')));
                                                        $shadow->setDistance(Drawing::EMUToPixels(self::getArrayItem($outerShdw->attributes(), 'dist')));
                                                        $shadow->setDirection(Drawing::angleToDegrees(self::getArrayItem($outerShdw->attributes(), 'dir')));
                                                        $shadow->setAlignment((string) self::getArrayItem($outerShdw->attributes(), 'algn'));
                                                        $clr = isset($outerShdw->srgbClr) ? $outerShdw->srgbClr : $outerShdw->prstClr;
                                                        $shadow->getColor()->setRGB(self::getArrayItem($clr->attributes(), 'val'));
                                                        $shadow->setAlpha(self::getArrayItem($clr->alpha->attributes(), 'val') / 1000);
                                                    }

                                                    $this->readHyperLinkDrawing($objDrawing, $twoCellAnchor, $hyperlinks);

                                                    $objDrawing->setWorksheet($docSheet);
                                                } elseif (($this->includeCharts) && ($twoCellAnchor->graphicFrame)) {
                                                    $fromCoordinate = Coordinate::stringFromColumnIndex(((string) $twoCellAnchor->from->col) + 1) . ($twoCellAnchor->from->row + 1);
                                                    $fromOffsetX = Drawing::EMUToPixels($twoCellAnchor->from->colOff);
                                                    $fromOffsetY = Drawing::EMUToPixels($twoCellAnchor->from->rowOff);
                                                    $toCoordinate = Coordinate::stringFromColumnIndex(((string) $twoCellAnchor->to->col) + 1) . ($twoCellAnchor->to->row + 1);
                                                    $toOffsetX = Drawing::EMUToPixels($twoCellAnchor->to->colOff);
                                                    $toOffsetY = Drawing::EMUToPixels($twoCellAnchor->to->rowOff);
                                                    $graphic = $twoCellAnchor->graphicFrame->children('http:
                                                    
                                                    $chartRef = $graphic->graphicData->children('http:
                                                    $thisChart = (string) $chartRef->attributes('http:

                                                    $chartDetails[$docSheet->getTitle() . '!' . $thisChart] = [
                                                        'fromCoordinate' => $fromCoordinate,
                                                        'fromOffsetX' => $fromOffsetX,
                                                        'fromOffsetY' => $fromOffsetY,
                                                        'toCoordinate' => $toCoordinate,
                                                        'toOffsetX' => $toOffsetX,
                                                        'toOffsetY' => $toOffsetY,
                                                        'worksheetTitle' => $docSheet->getTitle(),
                                                    ];
                                                }
                                            }
                                        }
                                        if ($relsDrawing === false && $xmlDrawing->count() == 0) {
                                            
                                            $unparsedDrawings[$drawingRelId] = $xmlDrawing->asXML();
                                        }
                                    }

                                    
                                    $unparsedLoadedData['sheets'][$docSheet->getCodeName()]['drawingOriginalIds'] = [];
                                    foreach ($relsWorksheet->Relationship as $ele) {
                                        if ($ele['Type'] == 'http:
                                            $drawingRelId = (string) $ele['Id'];
                                            $unparsedLoadedData['sheets'][$docSheet->getCodeName()]['drawingOriginalIds'][(string) $ele['Target']] = $drawingRelId;
                                            if (isset($unparsedDrawings[$drawingRelId])) {
                                                $unparsedLoadedData['sheets'][$docSheet->getCodeName()]['Drawings'][$drawingRelId] = $unparsedDrawings[$drawingRelId];
                                            }
                                        }
                                    }

                                    
                                    $xmlAltDrawing = simplexml_load_string(
                                        $this->securityScanner->scan($this->getFromZipArchive($zip, $fileDrawing)),
                                        'SimpleXMLElement',
                                        Settings::getLibXmlLoaderOptions()
                                    )->children('http:

                                    if ($xmlAltDrawing->AlternateContent) {
                                        foreach ($xmlAltDrawing->AlternateContent as $alternateContent) {
                                            $unparsedLoadedData['sheets'][$docSheet->getCodeName()]['drawingAlternateContents'][] = $alternateContent->asXML();
                                        }
                                    }
                                }
                            }

                            $this->readFormControlProperties($excel, $zip, $dir, $fileWorksheet, $docSheet, $unparsedLoadedData);
                            $this->readPrinterSettings($excel, $zip, $dir, $fileWorksheet, $docSheet, $unparsedLoadedData);

                            
                            if ($xmlWorkbook->definedNames) {
                                foreach ($xmlWorkbook->definedNames->definedName as $definedName) {
                                    
                                    $extractedRange = (string) $definedName;
                                    if (($spos = strpos($extractedRange, '!')) !== false) {
                                        $extractedRange = substr($extractedRange, 0, $spos) . str_replace('$', '', substr($extractedRange, $spos));
                                    } else {
                                        $extractedRange = str_replace('$', '', $extractedRange);
                                    }

                                    
                                    if ($extractedRange == '') {
                                        continue;
                                    }

                                    
                                    if ((string) $definedName['localSheetId'] != '' && (string) $definedName['localSheetId'] == $oldSheetId) {
                                        
                                        switch ((string) $definedName['name']) {
                                            case '_xlnm._FilterDatabase':
                                                if ((string) $definedName['hidden'] !== '1') {
                                                    $extractedRange = explode(',', $extractedRange);
                                                    foreach ($extractedRange as $range) {
                                                        $autoFilterRange = $range;
                                                        if (strpos($autoFilterRange, ':') !== false) {
                                                            $docSheet->getAutoFilter()->setRange($autoFilterRange);
                                                        }
                                                    }
                                                }

                                                break;
                                            case '_xlnm.Print_Titles':
                                                
                                                $extractedRange = explode(',', $extractedRange);

                                                
                                                foreach ($extractedRange as $range) {
                                                    $matches = [];
                                                    $range = str_replace('$', '', $range);

                                                    
                                                    if (preg_match('/!?([A-Z]+)\:([A-Z]+)$/', $range, $matches)) {
                                                        $docSheet->getPageSetup()->setColumnsToRepeatAtLeft([$matches[1], $matches[2]]);
                                                    } elseif (preg_match('/!?(\d+)\:(\d+)$/', $range, $matches)) {
                                                        
                                                        $docSheet->getPageSetup()->setRowsToRepeatAtTop([$matches[1], $matches[2]]);
                                                    }
                                                }

                                                break;
                                            case '_xlnm.Print_Area':
                                                $rangeSets = preg_split("/('?(?:.*?)'?(?:![A-Z0-9]+:[A-Z0-9]+)),?/", $extractedRange, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
                                                $newRangeSets = [];
                                                foreach ($rangeSets as $rangeSet) {
                                                    [$sheetName, $rangeSet] = Worksheet::extractSheetTitle($rangeSet, true);
                                                    if (strpos($rangeSet, ':') === false) {
                                                        $rangeSet = $rangeSet . ':' . $rangeSet;
                                                    }
                                                    $newRangeSets[] = str_replace('$', '', $rangeSet);
                                                }
                                                $docSheet->getPageSetup()->setPrintArea(implode(',', $newRangeSets));

                                                break;
                                            default:
                                                break;
                                        }
                                    }
                                }
                            }

                            
                            ++$sheetId;
                        }

                        
                        if ($xmlWorkbook->definedNames) {
                            foreach ($xmlWorkbook->definedNames->definedName as $definedName) {
                                
                                $extractedRange = (string) $definedName;

                                
                                if ($extractedRange == '') {
                                    continue;
                                }

                                
                                if ((string) $definedName['localSheetId'] != '') {
                                    
                                    
                                    switch ((string) $definedName['name']) {
                                        case '_xlnm._FilterDatabase':
                                        case '_xlnm.Print_Titles':
                                        case '_xlnm.Print_Area':
                                            break;
                                        default:
                                            if ($mapSheetId[(int) $definedName['localSheetId']] !== null) {
                                                $range = Worksheet::extractSheetTitle((string) $definedName, true);
                                                $scope = $excel->getSheet($mapSheetId[(int) $definedName['localSheetId']]);
                                                if (strpos((string) $definedName, '!') !== false) {
                                                    $range[0] = str_replace("''", "'", $range[0]);
                                                    $range[0] = str_replace("'", '', $range[0]);
                                                    if ($worksheet = $excel->getSheetByName($range[0])) {
                                                        $excel->addDefinedName(DefinedName::createInstance((string) $definedName['name'], $worksheet, $extractedRange, true, $scope));
                                                    } else {
                                                        $excel->addDefinedName(DefinedName::createInstance((string) $definedName['name'], $scope, $extractedRange, true, $scope));
                                                    }
                                                } else {
                                                    $excel->addDefinedName(DefinedName::createInstance((string) $definedName['name'], $scope, $extractedRange, true));
                                                }
                                            }

                                            break;
                                    }
                                } elseif (!isset($definedName['localSheetId'])) {
                                    $definedRange = (string) $definedName;
                                    
                                    $locatedSheet = null;
                                    if (strpos((string) $definedName, '!') !== false) {
                                        
                                        
                                        $definedNameValueParts = preg_split("/[ ,](?=([^']*'[^']*')*[^']*$)/miuU", $definedRange);
                                        
                                        [$extractedSheetName] = Worksheet::extractSheetTitle((string) $definedNameValueParts[0], true);
                                        $extractedSheetName = trim($extractedSheetName, "'");

                                        
                                        $locatedSheet = $excel->getSheetByName($extractedSheetName);
                                    }

                                    if ($locatedSheet === null && !DefinedName::testIfFormula($definedRange)) {
                                        $definedRange = '#REF!';
                                    }
                                    $excel->addDefinedName(DefinedName::createInstance((string) $definedName['name'], $locatedSheet, $definedRange, false));
                                }
                            }
                        }
                    }

                    if ((!$this->readDataOnly || !empty($this->loadSheetsOnly)) && isset($xmlWorkbook->bookViews->workbookView)) {
                        $workbookView = $xmlWorkbook->bookViews->workbookView;

                        
                        $activeTab = (int) ($workbookView['activeTab']); 

                        
                        if (isset($mapSheetId[$activeTab]) && $mapSheetId[$activeTab] !== null) {
                            $excel->setActiveSheetIndex($mapSheetId[$activeTab]);
                        } else {
                            if ($excel->getSheetCount() == 0) {
                                $excel->createSheet();
                            }
                            $excel->setActiveSheetIndex(0);
                        }

                        if (isset($workbookView['showHorizontalScroll'])) {
                            $showHorizontalScroll = (string) $workbookView['showHorizontalScroll'];
                            $excel->setShowHorizontalScroll($this->castXsdBooleanToBool($showHorizontalScroll));
                        }

                        if (isset($workbookView['showVerticalScroll'])) {
                            $showVerticalScroll = (string) $workbookView['showVerticalScroll'];
                            $excel->setShowVerticalScroll($this->castXsdBooleanToBool($showVerticalScroll));
                        }

                        if (isset($workbookView['showSheetTabs'])) {
                            $showSheetTabs = (string) $workbookView['showSheetTabs'];
                            $excel->setShowSheetTabs($this->castXsdBooleanToBool($showSheetTabs));
                        }

                        if (isset($workbookView['minimized'])) {
                            $minimized = (string) $workbookView['minimized'];
                            $excel->setMinimized($this->castXsdBooleanToBool($minimized));
                        }

                        if (isset($workbookView['autoFilterDateGrouping'])) {
                            $autoFilterDateGrouping = (string) $workbookView['autoFilterDateGrouping'];
                            $excel->setAutoFilterDateGrouping($this->castXsdBooleanToBool($autoFilterDateGrouping));
                        }

                        if (isset($workbookView['firstSheet'])) {
                            $firstSheet = (string) $workbookView['firstSheet'];
                            $excel->setFirstSheetIndex((int) $firstSheet);
                        }

                        if (isset($workbookView['visibility'])) {
                            $visibility = (string) $workbookView['visibility'];
                            $excel->setVisibility($visibility);
                        }

                        if (isset($workbookView['tabRatio'])) {
                            $tabRatio = (string) $workbookView['tabRatio'];
                            $excel->setTabRatio((int) $tabRatio);
                        }
                    }

                    break;
            }
        }

        if (!$this->readDataOnly) {
            $contentTypes = simplexml_load_string(
                $this->securityScanner->scan(
                    $this->getFromZipArchive($zip, '[Content_Types].xml')
                ),
                'SimpleXMLElement',
                Settings::getLibXmlLoaderOptions()
            );

            
            foreach ($contentTypes->Default as $contentType) {
                switch ($contentType['ContentType']) {
                    case 'application/vnd.openxmlformats-officedocument.spreadsheetml.printerSettings':
                        $unparsedLoadedData['default_content_types'][(string) $contentType['Extension']] = (string) $contentType['ContentType'];

                        break;
                }
            }

            
            foreach ($contentTypes->Override as $contentType) {
                switch ($contentType['ContentType']) {
                    case 'application/vnd.openxmlformats-officedocument.drawingml.chart+xml':
                        if ($this->includeCharts) {
                            $chartEntryRef = ltrim($contentType['PartName'], '/');
                            $chartElements = simplexml_load_string(
                                $this->securityScanner->scan(
                                    $this->getFromZipArchive($zip, $chartEntryRef)
                                ),
                                'SimpleXMLElement',
                                Settings::getLibXmlLoaderOptions()
                            );
                            $objChart = Chart::readChart($chartElements, basename($chartEntryRef, '.xml'));

                            if (isset($charts[$chartEntryRef])) {
                                $chartPositionRef = $charts[$chartEntryRef]['sheet'] . '!' . $charts[$chartEntryRef]['id'];
                                if (isset($chartDetails[$chartPositionRef])) {
                                    $excel->getSheetByName($charts[$chartEntryRef]['sheet'])->addChart($objChart);
                                    $objChart->setWorksheet($excel->getSheetByName($charts[$chartEntryRef]['sheet']));
                                    $objChart->setTopLeftPosition($chartDetails[$chartPositionRef]['fromCoordinate'], $chartDetails[$chartPositionRef]['fromOffsetX'], $chartDetails[$chartPositionRef]['fromOffsetY']);
                                    $objChart->setBottomRightPosition($chartDetails[$chartPositionRef]['toCoordinate'], $chartDetails[$chartPositionRef]['toOffsetX'], $chartDetails[$chartPositionRef]['toOffsetY']);
                                }
                            }
                        }

                        break;

                    
                    case 'application/vnd.ms-excel.controlproperties+xml':
                        $unparsedLoadedData['override_content_types'][(string) $contentType['PartName']] = (string) $contentType['ContentType'];

                        break;
                }
            }
        }

        $excel->setUnparsedLoadedData($unparsedLoadedData);

        $zip->close();

        return $excel;
    }

    private static function readColor($color, $background = false)
    {
        if (isset($color['rgb'])) {
            return (string) $color['rgb'];
        } elseif (isset($color['indexed'])) {
            return Color::indexedColor($color['indexed'] - 7, $background)->getARGB();
        } elseif (isset($color['theme'])) {
            if (self::$theme !== null) {
                $returnColour = self::$theme->getColourByIndex((int) $color['theme']);
                if (isset($color['tint'])) {
                    $tintAdjust = (float) $color['tint'];
                    $returnColour = Color::changeBrightness($returnColour, $tintAdjust);
                }

                return 'FF' . $returnColour;
            }
        }

        if ($background) {
            return 'FFFFFFFF';
        }

        return 'FF000000';
    }

    
    private static function readStyle(Style $docStyle, $style): void
    {
        $docStyle->getNumberFormat()->setFormatCode($style->numFmt);

        
        if (isset($style->font)) {
            $docStyle->getFont()->setName((string) $style->font->name['val']);
            $docStyle->getFont()->setSize((string) $style->font->sz['val']);
            if (isset($style->font->b)) {
                $docStyle->getFont()->setBold(!isset($style->font->b['val']) || self::boolean((string) $style->font->b['val']));
            }
            if (isset($style->font->i)) {
                $docStyle->getFont()->setItalic(!isset($style->font->i['val']) || self::boolean((string) $style->font->i['val']));
            }
            if (isset($style->font->strike)) {
                $docStyle->getFont()->setStrikethrough(!isset($style->font->strike['val']) || self::boolean((string) $style->font->strike['val']));
            }
            $docStyle->getFont()->getColor()->setARGB(self::readColor($style->font->color));

            if (isset($style->font->u) && !isset($style->font->u['val'])) {
                $docStyle->getFont()->setUnderline(\PhpOffice\PhpSpreadsheet\Style\Font::UNDERLINE_SINGLE);
            } elseif (isset($style->font->u, $style->font->u['val'])) {
                $docStyle->getFont()->setUnderline((string) $style->font->u['val']);
            }

            if (isset($style->font->vertAlign, $style->font->vertAlign['val'])) {
                $vertAlign = strtolower((string) $style->font->vertAlign['val']);
                if ($vertAlign == 'superscript') {
                    $docStyle->getFont()->setSuperscript(true);
                }
                if ($vertAlign == 'subscript') {
                    $docStyle->getFont()->setSubscript(true);
                }
            }
        }

        
        if (isset($style->fill)) {
            if ($style->fill->gradientFill) {
                
                $gradientFill = $style->fill->gradientFill[0];
                if (!empty($gradientFill['type'])) {
                    $docStyle->getFill()->setFillType((string) $gradientFill['type']);
                }
                $docStyle->getFill()->setRotation((float) ($gradientFill['degree']));
                $gradientFill->registerXPathNamespace('sml', 'http:
                $docStyle->getFill()->getStartColor()->setARGB(self::readColor(self::getArrayItem($gradientFill->xpath('sml:stop[@position=0]'))->color));
                $docStyle->getFill()->getEndColor()->setARGB(self::readColor(self::getArrayItem($gradientFill->xpath('sml:stop[@position=1]'))->color));
            } elseif ($style->fill->patternFill) {
                $patternType = (string) $style->fill->patternFill['patternType'] != '' ? (string) $style->fill->patternFill['patternType'] : 'solid';
                $docStyle->getFill()->setFillType($patternType);
                if ($style->fill->patternFill->fgColor) {
                    $docStyle->getFill()->getStartColor()->setARGB(self::readColor($style->fill->patternFill->fgColor, true));
                }
                if ($style->fill->patternFill->bgColor) {
                    $docStyle->getFill()->getEndColor()->setARGB(self::readColor($style->fill->patternFill->bgColor, true));
                }
            }
        }

        
        if (isset($style->border)) {
            $diagonalUp = self::boolean((string) $style->border['diagonalUp']);
            $diagonalDown = self::boolean((string) $style->border['diagonalDown']);
            if (!$diagonalUp && !$diagonalDown) {
                $docStyle->getBorders()->setDiagonalDirection(Borders::DIAGONAL_NONE);
            } elseif ($diagonalUp && !$diagonalDown) {
                $docStyle->getBorders()->setDiagonalDirection(Borders::DIAGONAL_UP);
            } elseif (!$diagonalUp && $diagonalDown) {
                $docStyle->getBorders()->setDiagonalDirection(Borders::DIAGONAL_DOWN);
            } else {
                $docStyle->getBorders()->setDiagonalDirection(Borders::DIAGONAL_BOTH);
            }
            self::readBorder($docStyle->getBorders()->getLeft(), $style->border->left);
            self::readBorder($docStyle->getBorders()->getRight(), $style->border->right);
            self::readBorder($docStyle->getBorders()->getTop(), $style->border->top);
            self::readBorder($docStyle->getBorders()->getBottom(), $style->border->bottom);
            self::readBorder($docStyle->getBorders()->getDiagonal(), $style->border->diagonal);
        }

        
        if (isset($style->alignment)) {
            $docStyle->getAlignment()->setHorizontal((string) $style->alignment['horizontal']);
            $docStyle->getAlignment()->setVertical((string) $style->alignment['vertical']);

            $textRotation = 0;
            if ((int) $style->alignment['textRotation'] <= 90) {
                $textRotation = (int) $style->alignment['textRotation'];
            } elseif ((int) $style->alignment['textRotation'] > 90) {
                $textRotation = 90 - (int) $style->alignment['textRotation'];
            }

            $docStyle->getAlignment()->setTextRotation((int) $textRotation);
            $docStyle->getAlignment()->setWrapText(self::boolean((string) $style->alignment['wrapText']));
            $docStyle->getAlignment()->setShrinkToFit(self::boolean((string) $style->alignment['shrinkToFit']));
            $docStyle->getAlignment()->setIndent((int) ((string) $style->alignment['indent']) > 0 ? (int) ((string) $style->alignment['indent']) : 0);
            $docStyle->getAlignment()->setReadOrder((int) ((string) $style->alignment['readingOrder']) > 0 ? (int) ((string) $style->alignment['readingOrder']) : 0);
        }

        
        if (isset($style->protection)) {
            if (isset($style->protection['locked'])) {
                if (self::boolean((string) $style->protection['locked'])) {
                    $docStyle->getProtection()->setLocked(Protection::PROTECTION_PROTECTED);
                } else {
                    $docStyle->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);
                }
            }

            if (isset($style->protection['hidden'])) {
                if (self::boolean((string) $style->protection['hidden'])) {
                    $docStyle->getProtection()->setHidden(Protection::PROTECTION_PROTECTED);
                } else {
                    $docStyle->getProtection()->setHidden(Protection::PROTECTION_UNPROTECTED);
                }
            }
        }

        
        if (isset($style->quotePrefix)) {
            $docStyle->setQuotePrefix($style->quotePrefix);
        }
    }

    
    private static function readBorder(Border $docBorder, $eleBorder): void
    {
        if (isset($eleBorder['style'])) {
            $docBorder->setBorderStyle((string) $eleBorder['style']);
        }
        if (isset($eleBorder->color)) {
            $docBorder->getColor()->setARGB(self::readColor($eleBorder->color));
        }
    }

    
    private function parseRichText($is)
    {
        $value = new RichText();

        if (isset($is->t)) {
            $value->createText(StringHelper::controlCharacterOOXML2PHP((string) $is->t));
        } else {
            if (is_object($is->r)) {
                foreach ($is->r as $run) {
                    if (!isset($run->rPr)) {
                        $value->createText(StringHelper::controlCharacterOOXML2PHP((string) $run->t));
                    } else {
                        $objText = $value->createTextRun(StringHelper::controlCharacterOOXML2PHP((string) $run->t));

                        if (isset($run->rPr->rFont['val'])) {
                            $objText->getFont()->setName((string) $run->rPr->rFont['val']);
                        }
                        if (isset($run->rPr->sz['val'])) {
                            $objText->getFont()->setSize((float) $run->rPr->sz['val']);
                        }
                        if (isset($run->rPr->color)) {
                            $objText->getFont()->setColor(new Color(self::readColor($run->rPr->color)));
                        }
                        if (
                            (isset($run->rPr->b['val']) && self::boolean((string) $run->rPr->b['val'])) ||
                            (isset($run->rPr->b) && !isset($run->rPr->b['val']))
                        ) {
                            $objText->getFont()->setBold(true);
                        }
                        if (
                            (isset($run->rPr->i['val']) && self::boolean((string) $run->rPr->i['val'])) ||
                            (isset($run->rPr->i) && !isset($run->rPr->i['val']))
                        ) {
                            $objText->getFont()->setItalic(true);
                        }
                        if (isset($run->rPr->vertAlign, $run->rPr->vertAlign['val'])) {
                            $vertAlign = strtolower((string) $run->rPr->vertAlign['val']);
                            if ($vertAlign == 'superscript') {
                                $objText->getFont()->setSuperscript(true);
                            }
                            if ($vertAlign == 'subscript') {
                                $objText->getFont()->setSubscript(true);
                            }
                        }
                        if (isset($run->rPr->u) && !isset($run->rPr->u['val'])) {
                            $objText->getFont()->setUnderline(\PhpOffice\PhpSpreadsheet\Style\Font::UNDERLINE_SINGLE);
                        } elseif (isset($run->rPr->u, $run->rPr->u['val'])) {
                            $objText->getFont()->setUnderline((string) $run->rPr->u['val']);
                        }
                        if (
                            (isset($run->rPr->strike['val']) && self::boolean((string) $run->rPr->strike['val'])) ||
                            (isset($run->rPr->strike) && !isset($run->rPr->strike['val']))
                        ) {
                            $objText->getFont()->setStrikethrough(true);
                        }
                    }
                }
            }
        }

        return $value;
    }

    
    private function readRibbon(Spreadsheet $excel, $customUITarget, $zip): void
    {
        $baseDir = dirname($customUITarget);
        $nameCustomUI = basename($customUITarget);
        
        $localRibbon = $this->getFromZipArchive($zip, $customUITarget);
        $customUIImagesNames = [];
        $customUIImagesBinaries = [];
        
        $pathRels = $baseDir . '/_rels/' . $nameCustomUI . '.rels';
        $dataRels = $this->getFromZipArchive($zip, $pathRels);
        if ($dataRels) {
            
            $UIRels = simplexml_load_string(
                $this->securityScanner->scan($dataRels),
                'SimpleXMLElement',
                Settings::getLibXmlLoaderOptions()
            );
            if (false !== $UIRels) {
                
                foreach ($UIRels->Relationship as $ele) {
                    if ($ele['Type'] == 'http:
                        
                        $customUIImagesNames[(string) $ele['Id']] = (string) $ele['Target'];
                        $customUIImagesBinaries[(string) $ele['Target']] = $this->getFromZipArchive($zip, $baseDir . '/' . (string) $ele['Target']);
                    }
                }
            }
        }
        if ($localRibbon) {
            $excel->setRibbonXMLData($customUITarget, $localRibbon);
            if (count($customUIImagesNames) > 0 && count($customUIImagesBinaries) > 0) {
                $excel->setRibbonBinObjects($customUIImagesNames, $customUIImagesBinaries);
            } else {
                $excel->setRibbonBinObjects(null, null);
            }
        } else {
            $excel->setRibbonXMLData(null, null);
            $excel->setRibbonBinObjects(null, null);
        }
    }

    private static function getArrayItem($array, $key = 0)
    {
        return $array[$key] ?? null;
    }

    private static function dirAdd($base, $add)
    {
        return preg_replace('~[^/]+/\.\./~', '', dirname($base) . "/$add");
    }

    private static function toCSSArray($style)
    {
        $style = self::stripWhiteSpaceFromStyleString($style);

        $temp = explode(';', $style);
        $style = [];
        foreach ($temp as $item) {
            $item = explode(':', $item);

            if (strpos($item[1], 'px') !== false) {
                $item[1] = str_replace('px', '', $item[1]);
            }
            if (strpos($item[1], 'pt') !== false) {
                $item[1] = str_replace('pt', '', $item[1]);
                $item[1] = Font::fontSizeToPixels($item[1]);
            }
            if (strpos($item[1], 'in') !== false) {
                $item[1] = str_replace('in', '', $item[1]);
                $item[1] = Font::inchSizeToPixels($item[1]);
            }
            if (strpos($item[1], 'cm') !== false) {
                $item[1] = str_replace('cm', '', $item[1]);
                $item[1] = Font::centimeterSizeToPixels($item[1]);
            }

            $style[$item[0]] = $item[1];
        }

        return $style;
    }

    public static function stripWhiteSpaceFromStyleString($string)
    {
        return trim(str_replace(["\r", "\n", ' '], '', $string), ';');
    }

    private static function boolean($value)
    {
        if (is_object($value)) {
            $value = (string) $value;
        }
        if (is_numeric($value)) {
            return (bool) $value;
        }

        return $value === 'true' || $value === 'TRUE';
    }

    
    private function readHyperLinkDrawing($objDrawing, $cellAnchor, $hyperlinks): void
    {
        $hlinkClick = $cellAnchor->pic->nvPicPr->cNvPr->children('http:

        if ($hlinkClick->count() === 0) {
            return;
        }

        $hlinkId = (string) $hlinkClick->attributes('http:
        $hyperlink = new Hyperlink(
            $hyperlinks[$hlinkId],
            (string) self::getArrayItem($cellAnchor->pic->nvPicPr->cNvPr->attributes(), 'name')
        );
        $objDrawing->setHyperlink($hyperlink);
    }

    private function readProtection(Spreadsheet $excel, SimpleXMLElement $xmlWorkbook): void
    {
        if (!$xmlWorkbook->workbookProtection) {
            return;
        }

        if ($xmlWorkbook->workbookProtection['lockRevision']) {
            $excel->getSecurity()->setLockRevision((bool) $xmlWorkbook->workbookProtection['lockRevision']);
        }

        if ($xmlWorkbook->workbookProtection['lockStructure']) {
            $excel->getSecurity()->setLockStructure((bool) $xmlWorkbook->workbookProtection['lockStructure']);
        }

        if ($xmlWorkbook->workbookProtection['lockWindows']) {
            $excel->getSecurity()->setLockWindows((bool) $xmlWorkbook->workbookProtection['lockWindows']);
        }

        if ($xmlWorkbook->workbookProtection['revisionsPassword']) {
            $excel->getSecurity()->setRevisionsPassword((string) $xmlWorkbook->workbookProtection['revisionsPassword'], true);
        }

        if ($xmlWorkbook->workbookProtection['workbookPassword']) {
            $excel->getSecurity()->setWorkbookPassword((string) $xmlWorkbook->workbookProtection['workbookPassword'], true);
        }
    }

    private function readFormControlProperties(Spreadsheet $excel, ZipArchive $zip, $dir, $fileWorksheet, $docSheet, array &$unparsedLoadedData): void
    {
        if (!$zip->locateName(dirname("$dir/$fileWorksheet") . '/_rels/' . basename($fileWorksheet) . '.rels')) {
            return;
        }

        
        $relsWorksheet = simplexml_load_string(
            $this->securityScanner->scan(
                $this->getFromZipArchive($zip, dirname("$dir/$fileWorksheet") . '/_rels/' . basename($fileWorksheet) . '.rels')
            ),
            'SimpleXMLElement',
            Settings::getLibXmlLoaderOptions()
        );
        $ctrlProps = [];
        foreach ($relsWorksheet->Relationship as $ele) {
            if ($ele['Type'] == 'http:
                $ctrlProps[(string) $ele['Id']] = $ele;
            }
        }

        $unparsedCtrlProps = &$unparsedLoadedData['sheets'][$docSheet->getCodeName()]['ctrlProps'];
        foreach ($ctrlProps as $rId => $ctrlProp) {
            $rId = substr($rId, 3); 
            $unparsedCtrlProps[$rId] = [];
            $unparsedCtrlProps[$rId]['filePath'] = self::dirAdd("$dir/$fileWorksheet", $ctrlProp['Target']);
            $unparsedCtrlProps[$rId]['relFilePath'] = (string) $ctrlProp['Target'];
            $unparsedCtrlProps[$rId]['content'] = $this->securityScanner->scan($this->getFromZipArchive($zip, $unparsedCtrlProps[$rId]['filePath']));
        }
        unset($unparsedCtrlProps);
    }

    private function readPrinterSettings(Spreadsheet $excel, ZipArchive $zip, $dir, $fileWorksheet, $docSheet, array &$unparsedLoadedData): void
    {
        if (!$zip->locateName(dirname("$dir/$fileWorksheet") . '/_rels/' . basename($fileWorksheet) . '.rels')) {
            return;
        }

        
        $relsWorksheet = simplexml_load_string(
            $this->securityScanner->scan(
                $this->getFromZipArchive($zip, dirname("$dir/$fileWorksheet") . '/_rels/' . basename($fileWorksheet) . '.rels')
            ),
            'SimpleXMLElement',
            Settings::getLibXmlLoaderOptions()
        );
        $sheetPrinterSettings = [];
        foreach ($relsWorksheet->Relationship as $ele) {
            if ($ele['Type'] == 'http:
                $sheetPrinterSettings[(string) $ele['Id']] = $ele;
            }
        }

        $unparsedPrinterSettings = &$unparsedLoadedData['sheets'][$docSheet->getCodeName()]['printerSettings'];
        foreach ($sheetPrinterSettings as $rId => $printerSettings) {
            $rId = substr($rId, 3) . 'ps'; 
            $unparsedPrinterSettings[$rId] = [];
            $unparsedPrinterSettings[$rId]['filePath'] = self::dirAdd("$dir/$fileWorksheet", $printerSettings['Target']);
            $unparsedPrinterSettings[$rId]['relFilePath'] = (string) $printerSettings['Target'];
            $unparsedPrinterSettings[$rId]['content'] = $this->securityScanner->scan($this->getFromZipArchive($zip, $unparsedPrinterSettings[$rId]['filePath']));
        }
        unset($unparsedPrinterSettings);
    }

    
    private function castXsdBooleanToBool($xsdBoolean)
    {
        if ($xsdBoolean === 'false') {
            return false;
        }

        return (bool) $xsdBoolean;
    }

    
    private function getWorkbookBaseName(ZipArchive $zip)
    {
        $workbookBasename = '';

        
        $rels = simplexml_load_string(
            $this->securityScanner->scan(
                $this->getFromZipArchive($zip, '_rels/.rels')
            ),
            'SimpleXMLElement',
            Settings::getLibXmlLoaderOptions()
        );
        if ($rels !== false) {
            foreach ($rels->Relationship as $rel) {
                switch ($rel['Type']) {
                    case 'http:
                        $basename = basename($rel['Target']);
                        if (preg_match('/workbook.*\.xml/', $basename)) {
                            $workbookBasename = $basename;
                        }

                        break;
                }
            }
        }

        return $workbookBasename;
    }

    private function readSheetProtection(Worksheet $docSheet, SimpleXMLElement $xmlSheet): void
    {
        if ($this->readDataOnly || !$xmlSheet->sheetProtection) {
            return;
        }

        $algorithmName = (string) $xmlSheet->sheetProtection['algorithmName'];
        $protection = $docSheet->getProtection();
        $protection->setAlgorithm($algorithmName);

        if ($algorithmName) {
            $protection->setPassword((string) $xmlSheet->sheetProtection['hashValue'], true);
            $protection->setSalt((string) $xmlSheet->sheetProtection['saltValue']);
            $protection->setSpinCount((int) $xmlSheet->sheetProtection['spinCount']);
        } else {
            $protection->setPassword((string) $xmlSheet->sheetProtection['password'], true);
        }

        if ($xmlSheet->protectedRanges->protectedRange) {
            foreach ($xmlSheet->protectedRanges->protectedRange as $protectedRange) {
                $docSheet->protectCells((string) $protectedRange['sqref'], (string) $protectedRange['password'], true);
            }
        }
    }
}
