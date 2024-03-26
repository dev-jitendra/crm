<?php

namespace PhpOffice\PhpSpreadsheet\Reader;

use DateTime;
use DateTimeZone;
use DOMAttr;
use DOMDocument;
use DOMElement;
use DOMNode;
use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\DefinedName;
use PhpOffice\PhpSpreadsheet\Reader\Exception as ReaderException;
use PhpOffice\PhpSpreadsheet\Reader\Ods\PageSettings;
use PhpOffice\PhpSpreadsheet\Reader\Ods\Properties as DocumentProperties;
use PhpOffice\PhpSpreadsheet\Reader\Security\XmlScanner;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Settings;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use XMLReader;
use ZipArchive;

class Ods extends BaseReader
{
    
    public function __construct()
    {
        parent::__construct();
        $this->securityScanner = XmlScanner::getInstance($this);
    }

    
    public function canRead($pFilename)
    {
        File::assertFile($pFilename);

        $mimeType = 'UNKNOWN';

        

        $zip = new ZipArchive();
        if ($zip->open($pFilename) === true) {
            
            $stat = $zip->statName('mimetype');
            if ($stat && ($stat['size'] <= 255)) {
                $mimeType = $zip->getFromName($stat['name']);
            } elseif ($zip->statName('META-INF/manifest.xml')) {
                $xml = simplexml_load_string(
                    $this->securityScanner->scan($zip->getFromName('META-INF/manifest.xml')),
                    'SimpleXMLElement',
                    Settings::getLibXmlLoaderOptions()
                );
                $namespacesContent = $xml->getNamespaces(true);
                if (isset($namespacesContent['manifest'])) {
                    $manifest = $xml->children($namespacesContent['manifest']);
                    foreach ($manifest as $manifestDataSet) {
                        $manifestAttributes = $manifestDataSet->attributes($namespacesContent['manifest']);
                        if ($manifestAttributes->{'full-path'} == '/') {
                            $mimeType = (string) $manifestAttributes->{'media-type'};

                            break;
                        }
                    }
                }
            }

            $zip->close();
        }

        return $mimeType === 'application/vnd.oasis.opendocument.spreadsheet';
    }

    
    public function listWorksheetNames($pFilename)
    {
        File::assertFile($pFilename);

        $zip = new ZipArchive();
        if ($zip->open($pFilename) !== true) {
            throw new ReaderException('Could not open ' . $pFilename . ' for reading! Error opening file.');
        }

        $worksheetNames = [];

        $xml = new XMLReader();
        $xml->xml(
            $this->securityScanner->scanFile('zip:
            null,
            Settings::getLibXmlLoaderOptions()
        );
        $xml->setParserProperty(2, true);

        
        $xml->read();
        while ($xml->read()) {
            
            while ($xml->name !== 'office:body') {
                if ($xml->isEmptyElement) {
                    $xml->read();
                } else {
                    $xml->next();
                }
            }
            
            while ($xml->read()) {
                if ($xml->name == 'table:table' && $xml->nodeType == XMLReader::ELEMENT) {
                    
                    do {
                        $worksheetNames[] = $xml->getAttribute('table:name');
                        $xml->next();
                    } while ($xml->name == 'table:table' && $xml->nodeType == XMLReader::ELEMENT);
                }
            }
        }

        return $worksheetNames;
    }

    
    public function listWorksheetInfo($pFilename)
    {
        File::assertFile($pFilename);

        $worksheetInfo = [];

        $zip = new ZipArchive();
        if ($zip->open($pFilename) !== true) {
            throw new ReaderException('Could not open ' . $pFilename . ' for reading! Error opening file.');
        }

        $xml = new XMLReader();
        $xml->xml(
            $this->securityScanner->scanFile('zip:
            null,
            Settings::getLibXmlLoaderOptions()
        );
        $xml->setParserProperty(2, true);

        
        $xml->read();
        while ($xml->read()) {
            
            while ($xml->name !== 'office:body') {
                if ($xml->isEmptyElement) {
                    $xml->read();
                } else {
                    $xml->next();
                }
            }
            
            while ($xml->read()) {
                if ($xml->name == 'table:table' && $xml->nodeType == XMLReader::ELEMENT) {
                    $worksheetNames[] = $xml->getAttribute('table:name');

                    $tmpInfo = [
                        'worksheetName' => $xml->getAttribute('table:name'),
                        'lastColumnLetter' => 'A',
                        'lastColumnIndex' => 0,
                        'totalRows' => 0,
                        'totalColumns' => 0,
                    ];

                    
                    $currCells = 0;
                    do {
                        $xml->read();
                        if ($xml->name == 'table:table-row' && $xml->nodeType == XMLReader::ELEMENT) {
                            $rowspan = $xml->getAttribute('table:number-rows-repeated');
                            $rowspan = empty($rowspan) ? 1 : $rowspan;
                            $tmpInfo['totalRows'] += $rowspan;
                            $tmpInfo['totalColumns'] = max($tmpInfo['totalColumns'], $currCells);
                            $currCells = 0;
                            
                            $xml->read();
                            do {
                                $doread = true;
                                if ($xml->name == 'table:table-cell' && $xml->nodeType == XMLReader::ELEMENT) {
                                    if (!$xml->isEmptyElement) {
                                        ++$currCells;
                                        $xml->next();
                                        $doread = false;
                                    }
                                } elseif ($xml->name == 'table:covered-table-cell' && $xml->nodeType == XMLReader::ELEMENT) {
                                    $mergeSize = $xml->getAttribute('table:number-columns-repeated');
                                    $currCells += (int) $mergeSize;
                                }
                                if ($doread) {
                                    $xml->read();
                                }
                            } while ($xml->name != 'table:table-row');
                        }
                    } while ($xml->name != 'table:table');

                    $tmpInfo['totalColumns'] = max($tmpInfo['totalColumns'], $currCells);
                    $tmpInfo['lastColumnIndex'] = $tmpInfo['totalColumns'] - 1;
                    $tmpInfo['lastColumnLetter'] = Coordinate::stringFromColumnIndex($tmpInfo['lastColumnIndex'] + 1);
                    $worksheetInfo[] = $tmpInfo;
                }
            }
        }

        return $worksheetInfo;
    }

    
    public function load($pFilename)
    {
        
        $spreadsheet = new Spreadsheet();

        
        return $this->loadIntoExisting($pFilename, $spreadsheet);
    }

    
    public function loadIntoExisting($pFilename, Spreadsheet $spreadsheet)
    {
        File::assertFile($pFilename);

        $timezoneObj = new DateTimeZone('Europe/London');
        $GMT = new DateTimeZone('UTC');

        $zip = new ZipArchive();
        if ($zip->open($pFilename) !== true) {
            throw new Exception("Could not open {$pFilename} for reading! Error opening file.");
        }

        

        $xml = @simplexml_load_string(
            $this->securityScanner->scan($zip->getFromName('meta.xml')),
            'SimpleXMLElement',
            Settings::getLibXmlLoaderOptions()
        );
        if ($xml === false) {
            throw new Exception('Unable to read data from {$pFilename}');
        }

        $namespacesMeta = $xml->getNamespaces(true);

        (new DocumentProperties($spreadsheet))->load($xml, $namespacesMeta);

        

        $dom = new DOMDocument('1.01', 'UTF-8');
        $dom->loadXML(
            $this->securityScanner->scan($zip->getFromName('styles.xml')),
            Settings::getLibXmlLoaderOptions()
        );

        $pageSettings = new PageSettings($dom);

        

        $dom = new DOMDocument('1.01', 'UTF-8');
        $dom->loadXML(
            $this->securityScanner->scan($zip->getFromName('content.xml')),
            Settings::getLibXmlLoaderOptions()
        );

        $officeNs = $dom->lookupNamespaceUri('office');
        $tableNs = $dom->lookupNamespaceUri('table');
        $textNs = $dom->lookupNamespaceUri('text');
        $xlinkNs = $dom->lookupNamespaceUri('xlink');

        $pageSettings->readStyleCrossReferences($dom);

        

        $spreadsheets = $dom->getElementsByTagNameNS($officeNs, 'body')
            ->item(0)
            ->getElementsByTagNameNS($officeNs, 'spreadsheet');

        foreach ($spreadsheets as $workbookData) {
            
            $tables = $workbookData->getElementsByTagNameNS($tableNs, 'table');

            $worksheetID = 0;
            foreach ($tables as $worksheetDataSet) {
                
                $worksheetName = $worksheetDataSet->getAttributeNS($tableNs, 'name');

                
                if (
                    isset($this->loadSheetsOnly)
                    && $worksheetName
                    && !in_array($worksheetName, $this->loadSheetsOnly)
                ) {
                    continue;
                }

                $worksheetStyleName = $worksheetDataSet->getAttributeNS($tableNs, 'style-name');

                
                if ($worksheetID > 0) {
                    $spreadsheet->createSheet(); 
                }
                $spreadsheet->setActiveSheetIndex($worksheetID);

                if ($worksheetName) {
                    
                    
                    
                    $spreadsheet->getActiveSheet()->setTitle((string) $worksheetName, false, false);
                }

                
                $rowID = 1;
                foreach ($worksheetDataSet->childNodes as $childNode) {
                    

                    
                    if ($childNode->namespaceURI != $tableNs) {
                        continue;
                    }

                    $key = $childNode->nodeName;

                    
                    if (strpos($key, ':') !== false) {
                        $keyChunks = explode(':', $key);
                        $key = array_pop($keyChunks);
                    }

                    switch ($key) {
                        case 'table-header-rows':
                            
                            
                            





                            break;
                        case 'table-row':
                            if ($childNode->hasAttributeNS($tableNs, 'number-rows-repeated')) {
                                $rowRepeats = $childNode->getAttributeNS($tableNs, 'number-rows-repeated');
                            } else {
                                $rowRepeats = 1;
                            }

                            $columnID = 'A';
                            foreach ($childNode->childNodes as $key => $cellData) {
                                

                                if ($this->getReadFilter() !== null) {
                                    if (!$this->getReadFilter()->readCell($columnID, $rowID, $worksheetName)) {
                                        ++$columnID;

                                        continue;
                                    }
                                }

                                
                                $formatting = $hyperlink = null;
                                $hasCalculatedValue = false;
                                $cellDataFormula = '';

                                if ($cellData->hasAttributeNS($tableNs, 'formula')) {
                                    $cellDataFormula = $cellData->getAttributeNS($tableNs, 'formula');
                                    $hasCalculatedValue = true;
                                }

                                
                                $annotation = $cellData->getElementsByTagNameNS($officeNs, 'annotation');

                                if ($annotation->length > 0) {
                                    $textNode = $annotation->item(0)->getElementsByTagNameNS($textNs, 'p');

                                    if ($textNode->length > 0) {
                                        $text = $this->scanElementForText($textNode->item(0));

                                        $spreadsheet->getActiveSheet()
                                            ->getComment($columnID . $rowID)
                                            ->setText($this->parseRichText($text));

                                    }
                                }

                                

                                
                                $paragraphs = [];

                                foreach ($cellData->childNodes as $item) {
                                    

                                    
                                    if ($item->nodeName == 'text:p') {
                                        $paragraphs[] = $item;
                                    }
                                }

                                if (count($paragraphs) > 0) {
                                    
                                    $dataArray = [];

                                    
                                    
                                    
                                    

                                    foreach ($paragraphs as $pData) {
                                        $dataArray[] = $this->scanElementForText($pData);
                                    }
                                    $allCellDataText = implode("\n", $dataArray);

                                    $type = $cellData->getAttributeNS($officeNs, 'value-type');

                                    switch ($type) {
                                        case 'string':
                                            $type = DataType::TYPE_STRING;
                                            $dataValue = $allCellDataText;

                                            foreach ($paragraphs as $paragraph) {
                                                $link = $paragraph->getElementsByTagNameNS($textNs, 'a');
                                                if ($link->length > 0) {
                                                    $hyperlink = $link->item(0)->getAttributeNS($xlinkNs, 'href');
                                                }
                                            }

                                            break;
                                        case 'boolean':
                                            $type = DataType::TYPE_BOOL;
                                            $dataValue = ($allCellDataText == 'TRUE') ? true : false;

                                            break;
                                        case 'percentage':
                                            $type = DataType::TYPE_NUMERIC;
                                            $dataValue = (float) $cellData->getAttributeNS($officeNs, 'value');

                                            
                                            
                                            
                                            
                                            $formatting = NumberFormat::FORMAT_PERCENTAGE_00;

                                            break;
                                        case 'currency':
                                            $type = DataType::TYPE_NUMERIC;
                                            $dataValue = (float) $cellData->getAttributeNS($officeNs, 'value');

                                            if (floor($dataValue) == $dataValue) {
                                                $dataValue = (int) $dataValue;
                                            }
                                            $formatting = NumberFormat::FORMAT_CURRENCY_USD_SIMPLE;

                                            break;
                                        case 'float':
                                            $type = DataType::TYPE_NUMERIC;
                                            $dataValue = (float) $cellData->getAttributeNS($officeNs, 'value');

                                            if (floor($dataValue) == $dataValue) {
                                                if ($dataValue == (int) $dataValue) {
                                                    $dataValue = (int) $dataValue;
                                                }
                                            }

                                            break;
                                        case 'date':
                                            $type = DataType::TYPE_NUMERIC;
                                            $value = $cellData->getAttributeNS($officeNs, 'date-value');

                                            $dateObj = new DateTime($value, $GMT);
                                            $dateObj->setTimeZone($timezoneObj);
                                            [$year, $month, $day, $hour, $minute, $second] = explode(
                                                ' ',
                                                $dateObj->format('Y m d H i s')
                                            );

                                            $dataValue = Date::formattedPHPToExcel(
                                                (int) $year,
                                                (int) $month,
                                                (int) $day,
                                                (int) $hour,
                                                (int) $minute,
                                                (int) $second
                                            );

                                            if ($dataValue != floor($dataValue)) {
                                                $formatting = NumberFormat::FORMAT_DATE_XLSX15
                                                    . ' '
                                                    . NumberFormat::FORMAT_DATE_TIME4;
                                            } else {
                                                $formatting = NumberFormat::FORMAT_DATE_XLSX15;
                                            }

                                            break;
                                        case 'time':
                                            $type = DataType::TYPE_NUMERIC;

                                            $timeValue = $cellData->getAttributeNS($officeNs, 'time-value');

                                            $dataValue = Date::PHPToExcel(
                                                strtotime(
                                                    '01-01-1970 ' . implode(':', sscanf($timeValue, 'PT%dH%dM%dS'))
                                                )
                                            );
                                            $formatting = NumberFormat::FORMAT_DATE_TIME4;

                                            break;
                                        default:
                                            $dataValue = null;
                                    }
                                } else {
                                    $type = DataType::TYPE_NULL;
                                    $dataValue = null;
                                }

                                if ($hasCalculatedValue) {
                                    $type = DataType::TYPE_FORMULA;
                                    $cellDataFormula = substr($cellDataFormula, strpos($cellDataFormula, ':=') + 1);
                                    $cellDataFormula = $this->convertToExcelFormulaValue($cellDataFormula);
                                }

                                if ($cellData->hasAttributeNS($tableNs, 'number-columns-repeated')) {
                                    $colRepeats = (int) $cellData->getAttributeNS($tableNs, 'number-columns-repeated');
                                } else {
                                    $colRepeats = 1;
                                }

                                if ($type !== null) {
                                    for ($i = 0; $i < $colRepeats; ++$i) {
                                        if ($i > 0) {
                                            ++$columnID;
                                        }

                                        if ($type !== DataType::TYPE_NULL) {
                                            for ($rowAdjust = 0; $rowAdjust < $rowRepeats; ++$rowAdjust) {
                                                $rID = $rowID + $rowAdjust;

                                                $cell = $spreadsheet->getActiveSheet()
                                                    ->getCell($columnID . $rID);

                                                
                                                if ($hasCalculatedValue) {
                                                    $cell->setValueExplicit($cellDataFormula, $type);
                                                } else {
                                                    $cell->setValueExplicit($dataValue, $type);
                                                }

                                                if ($hasCalculatedValue) {
                                                    $cell->setCalculatedValue($dataValue);
                                                }

                                                
                                                if ($formatting !== null) {
                                                    $spreadsheet->getActiveSheet()
                                                        ->getStyle($columnID . $rID)
                                                        ->getNumberFormat()
                                                        ->setFormatCode($formatting);
                                                } else {
                                                    $spreadsheet->getActiveSheet()
                                                        ->getStyle($columnID . $rID)
                                                        ->getNumberFormat()
                                                        ->setFormatCode(NumberFormat::FORMAT_GENERAL);
                                                }

                                                if ($hyperlink !== null) {
                                                    $cell->getHyperlink()
                                                        ->setUrl($hyperlink);
                                                }
                                            }
                                        }
                                    }
                                }

                                
                                if (
                                    $cellData->hasAttributeNS($tableNs, 'number-columns-spanned')
                                    || $cellData->hasAttributeNS($tableNs, 'number-rows-spanned')
                                ) {
                                    if (($type !== DataType::TYPE_NULL) || (!$this->readDataOnly)) {
                                        $columnTo = $columnID;

                                        if ($cellData->hasAttributeNS($tableNs, 'number-columns-spanned')) {
                                            $columnIndex = Coordinate::columnIndexFromString($columnID);
                                            $columnIndex += (int) $cellData->getAttributeNS($tableNs, 'number-columns-spanned');
                                            $columnIndex -= 2;

                                            $columnTo = Coordinate::stringFromColumnIndex($columnIndex + 1);
                                        }

                                        $rowTo = $rowID;

                                        if ($cellData->hasAttributeNS($tableNs, 'number-rows-spanned')) {
                                            $rowTo = $rowTo + (int) $cellData->getAttributeNS($tableNs, 'number-rows-spanned') - 1;
                                        }

                                        $cellRange = $columnID . $rowID . ':' . $columnTo . $rowTo;
                                        $spreadsheet->getActiveSheet()->mergeCells($cellRange);
                                    }
                                }

                                ++$columnID;
                            }
                            $rowID += $rowRepeats;

                            break;
                    }
                }
                $pageSettings->setPrintSettingsForWorksheet($spreadsheet->getActiveSheet(), $worksheetStyleName);
                ++$worksheetID;
            }

            $this->readDefinedRanges($spreadsheet, $workbookData, $tableNs);
            $this->readDefinedExpressions($spreadsheet, $workbookData, $tableNs);
        }
        $spreadsheet->setActiveSheetIndex(0);
        
        return $spreadsheet;
    }

    
    protected function scanElementForText(DOMNode $element)
    {
        $str = '';
        foreach ($element->childNodes as $child) {
            
            if ($child->nodeType == XML_TEXT_NODE) {
                $str .= $child->nodeValue;
            } elseif ($child->nodeType == XML_ELEMENT_NODE && $child->nodeName == 'text:s') {
                

                
                
                $cAttr = $child->attributes->getNamedItem('c');
                if ($cAttr) {
                    $multiplier = (int) $cAttr->nodeValue;
                } else {
                    $multiplier = 1;
                }

                $str .= str_repeat(' ', $multiplier);
            }

            if ($child->hasChildNodes()) {
                $str .= $this->scanElementForText($child);
            }
        }

        return $str;
    }

    
    private function parseRichText($is)
    {
        $value = new RichText();
        $value->createText($is);

        return $value;
    }

    private function convertToExcelAddressValue(string $openOfficeAddress): string
    {
        $excelAddress = $openOfficeAddress;

        
        
        
        $excelAddress = preg_replace('/\$?([^\.]+)\.([^\.]+):\$?([^\.]+)\.([^\.]+)/miu', '$1!$2:$4', $excelAddress);
        
        $excelAddress = preg_replace('/\$?([^\.]+)\.([^\.]+):\.([^\.]+)/miu', '$1!$2:$3', $excelAddress);
        
        $excelAddress = preg_replace('/\$?([^\.]+)\.([^\.]+)/miu', '$1!$2', $excelAddress);
        
        $excelAddress = preg_replace('/\.([^\.]+):\.([^\.]+)/miu', '$1:$2', $excelAddress);
        
        $excelAddress = preg_replace('/\.([^\.]+)/miu', '$1', $excelAddress);

        return $excelAddress;
    }

    private function convertToExcelFormulaValue(string $openOfficeFormula): string
    {
        $temp = explode('"', $openOfficeFormula);
        $tKey = false;
        foreach ($temp as &$value) {
            
            if ($tKey = !$tKey) {
                
                $value = preg_replace('/\[\$?([^\.]+)\.([^\.]+):\.([^\.]+)\]/miu', '$1!$2:$3', $value);
                
                $value = preg_replace('/\[\$?([^\.]+)\.([^\.]+)\]/miu', '$1!$2', $value);
                
                $value = preg_replace('/\[\.([^\.]+):\.([^\.]+)\]/miu', '$1:$2', $value);
                
                $value = preg_replace('/\[\.([^\.]+)\]/miu', '$1', $value);

                $value = Calculation::translateSeparator(';', ',', $value, $inBraces);
            }
        }

        
        $excelFormula = implode('"', $temp);

        return $excelFormula;
    }

    
    private function readDefinedRanges(Spreadsheet $spreadsheet, DOMElement $workbookData, string $tableNs): void
    {
        $namedRanges = $workbookData->getElementsByTagNameNS($tableNs, 'named-range');
        foreach ($namedRanges as $definedNameElement) {
            $definedName = $definedNameElement->getAttributeNS($tableNs, 'name');
            $baseAddress = $definedNameElement->getAttributeNS($tableNs, 'base-cell-address');
            $range = $definedNameElement->getAttributeNS($tableNs, 'cell-range-address');

            $baseAddress = $this->convertToExcelAddressValue($baseAddress);
            $range = $this->convertToExcelAddressValue($range);

            $this->addDefinedName($spreadsheet, $baseAddress, $definedName, $range);
        }
    }

    
    private function readDefinedExpressions(Spreadsheet $spreadsheet, DOMElement $workbookData, string $tableNs): void
    {
        $namedExpressions = $workbookData->getElementsByTagNameNS($tableNs, 'named-expression');
        foreach ($namedExpressions as $definedNameElement) {
            $definedName = $definedNameElement->getAttributeNS($tableNs, 'name');
            $baseAddress = $definedNameElement->getAttributeNS($tableNs, 'base-cell-address');
            $expression = $definedNameElement->getAttributeNS($tableNs, 'expression');

            $baseAddress = $this->convertToExcelAddressValue($baseAddress);
            $expression = $this->convertToExcelFormulaValue($expression);

            $this->addDefinedName($spreadsheet, $baseAddress, $definedName, $expression);
        }
    }

    
    private function addDefinedName(Spreadsheet $spreadsheet, string $baseAddress, string $definedName, string $value): void
    {
        [$sheetReference] = Worksheet::extractSheetTitle($baseAddress, true);
        $worksheet = $spreadsheet->getSheetByName($sheetReference);
        
        if ($worksheet !== null) {
            $spreadsheet->addDefinedName(DefinedName::createInstance((string) $definedName, $worksheet, $value));
        }
    }
}
