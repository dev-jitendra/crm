<?php

namespace PhpOffice\PhpSpreadsheet\Reader;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMText;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Reader\Security\XmlScanner;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Throwable;


class Html extends BaseReader
{
    
    const TEST_SAMPLE_SIZE = 2048;

    
    protected $inputEncoding = 'ANSI';

    
    protected $sheetIndex = 0;

    
    protected $formats = [
        'h1' => [
            'font' => [
                'bold' => true,
                'size' => 24,
            ],
        ], 
        'h2' => [
            'font' => [
                'bold' => true,
                'size' => 18,
            ],
        ], 
        'h3' => [
            'font' => [
                'bold' => true,
                'size' => 13.5,
            ],
        ], 
        'h4' => [
            'font' => [
                'bold' => true,
                'size' => 12,
            ],
        ], 
        'h5' => [
            'font' => [
                'bold' => true,
                'size' => 10,
            ],
        ], 
        'h6' => [
            'font' => [
                'bold' => true,
                'size' => 7.5,
            ],
        ], 
        'a' => [
            'font' => [
                'underline' => true,
                'color' => [
                    'argb' => Color::COLOR_BLUE,
                ],
            ],
        ], 
        'hr' => [
            'borders' => [
                'bottom' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => [
                        Color::COLOR_BLACK,
                    ],
                ],
            ],
        ], 
        'strong' => [
            'font' => [
                'bold' => true,
            ],
        ], 
        'b' => [
            'font' => [
                'bold' => true,
            ],
        ], 
        'i' => [
            'font' => [
                'italic' => true,
            ],
        ], 
        'em' => [
            'font' => [
                'italic' => true,
            ],
        ], 
    ];

    protected $rowspan = [];

    
    public function __construct()
    {
        parent::__construct();
        $this->securityScanner = XmlScanner::getInstance($this);
    }

    
    public function canRead($pFilename)
    {
        
        try {
            $this->openFile($pFilename);
        } catch (Exception $e) {
            return false;
        }

        $beginning = $this->readBeginning();
        $startWithTag = self::startsWithTag($beginning);
        $containsTags = self::containsTags($beginning);
        $endsWithTag = self::endsWithTag($this->readEnding());

        fclose($this->fileHandle);

        return $startWithTag && $containsTags && $endsWithTag;
    }

    private function readBeginning()
    {
        fseek($this->fileHandle, 0);

        return fread($this->fileHandle, self::TEST_SAMPLE_SIZE);
    }

    private function readEnding()
    {
        $meta = stream_get_meta_data($this->fileHandle);
        $filename = $meta['uri'];

        $size = filesize($filename);
        if ($size === 0) {
            return '';
        }

        $blockSize = self::TEST_SAMPLE_SIZE;
        if ($size < $blockSize) {
            $blockSize = $size;
        }

        fseek($this->fileHandle, $size - $blockSize);

        return fread($this->fileHandle, $blockSize);
    }

    private static function startsWithTag($data)
    {
        return '<' === substr(trim($data), 0, 1);
    }

    private static function endsWithTag($data)
    {
        return '>' === substr(trim($data), -1, 1);
    }

    private static function containsTags($data)
    {
        return strlen($data) !== strlen(strip_tags($data));
    }

    
    public function load($pFilename)
    {
        
        $spreadsheet = new Spreadsheet();

        
        return $this->loadIntoExisting($pFilename, $spreadsheet);
    }

    
    public function setInputEncoding($pValue)
    {
        $this->inputEncoding = $pValue;

        return $this;
    }

    
    public function getInputEncoding()
    {
        return $this->inputEncoding;
    }

    
    protected $dataArray = [];

    protected $tableLevel = 0;

    protected $nestedColumn = ['A'];

    protected function setTableStartColumn($column)
    {
        if ($this->tableLevel == 0) {
            $column = 'A';
        }
        ++$this->tableLevel;
        $this->nestedColumn[$this->tableLevel] = $column;

        return $this->nestedColumn[$this->tableLevel];
    }

    protected function getTableStartColumn()
    {
        return $this->nestedColumn[$this->tableLevel];
    }

    protected function releaseTableStartColumn()
    {
        --$this->tableLevel;

        return array_pop($this->nestedColumn);
    }

    protected function flushCell(Worksheet $sheet, $column, $row, &$cellContent): void
    {
        if (is_string($cellContent)) {
            
            if (trim($cellContent) > '') {
                
                
                
                $sheet->setCellValue($column . $row, $cellContent);
                $this->dataArray[$row][$column] = $cellContent;
            }
        } else {
            
            
            $this->dataArray[$row][$column] = 'RICH TEXT: ' . $cellContent;
        }
        $cellContent = (string) '';
    }

    private function processDomElementBody(Worksheet $sheet, int &$row, string &$column, string &$cellContent, DOMElement $child): void
    {
        $attributeArray = [];
        foreach ($child->attributes as $attribute) {
            $attributeArray[$attribute->name] = $attribute->value;
        }

        if ($child->nodeName === 'body') {
            $row = 1;
            $column = 'A';
            $cellContent = '';
            $this->tableLevel = 0;
            $this->processDomElement($child, $sheet, $row, $column, $cellContent);
        } else {
            $this->processDomElementTitle($sheet, $row, $column, $cellContent, $child, $attributeArray);
        }
    }

    private function processDomElementTitle(Worksheet $sheet, int &$row, string &$column, string &$cellContent, DOMElement $child, array &$attributeArray): void
    {
        if ($child->nodeName === 'title') {
            $this->processDomElement($child, $sheet, $row, $column, $cellContent);
            $sheet->setTitle($cellContent, true, false);
            $cellContent = '';
        } else {
            $this->processDomElementSpanEtc($sheet, $row, $column, $cellContent, $child, $attributeArray);
        }
    }

    private static $spanEtc = ['span', 'div', 'font', 'i', 'em', 'strong', 'b'];

    private function processDomElementSpanEtc(Worksheet $sheet, int &$row, string &$column, string &$cellContent, DOMElement $child, array &$attributeArray): void
    {
        if (in_array($child->nodeName, self::$spanEtc)) {
            if (isset($attributeArray['class']) && $attributeArray['class'] === 'comment') {
                $sheet->getComment($column . $row)
                    ->getText()
                    ->createTextRun($child->textContent);
            }
            $this->processDomElement($child, $sheet, $row, $column, $cellContent);

            if (isset($this->formats[$child->nodeName])) {
                $sheet->getStyle($column . $row)->applyFromArray($this->formats[$child->nodeName]);
            }
        } else {
            $this->processDomElementHr($sheet, $row, $column, $cellContent, $child, $attributeArray);
        }
    }

    private function processDomElementHr(Worksheet $sheet, int &$row, string &$column, string &$cellContent, DOMElement $child, array &$attributeArray): void
    {
        if ($child->nodeName === 'hr') {
            $this->flushCell($sheet, $column, $row, $cellContent);
            ++$row;
            if (isset($this->formats[$child->nodeName])) {
                $sheet->getStyle($column . $row)->applyFromArray($this->formats[$child->nodeName]);
            }
            ++$row;
        }
        
        $this->processDomElementBr($sheet, $row, $column, $cellContent, $child, $attributeArray);
    }

    private function processDomElementBr(Worksheet $sheet, int &$row, string &$column, string &$cellContent, DOMElement $child, array &$attributeArray): void
    {
        if ($child->nodeName === 'br' || $child->nodeName === 'hr') {
            if ($this->tableLevel > 0) {
                
                $cellContent .= "\n";
                $sheet->getStyle($column . $row)->getAlignment()->setWrapText(true);
            } else {
                
                $this->flushCell($sheet, $column, $row, $cellContent);
                ++$row;
            }
        } else {
            $this->processDomElementA($sheet, $row, $column, $cellContent, $child, $attributeArray);
        }
    }

    private function processDomElementA(Worksheet $sheet, int &$row, string &$column, string &$cellContent, DOMElement $child, array &$attributeArray): void
    {
        if ($child->nodeName === 'a') {
            foreach ($attributeArray as $attributeName => $attributeValue) {
                switch ($attributeName) {
                    case 'href':
                        $sheet->getCell($column . $row)->getHyperlink()->setUrl($attributeValue);
                        if (isset($this->formats[$child->nodeName])) {
                            $sheet->getStyle($column . $row)->applyFromArray($this->formats[$child->nodeName]);
                        }

                        break;
                    case 'class':
                        if ($attributeValue === 'comment-indicator') {
                            break; 
                        }
                }
            }
            
            
            $this->processDomElement($child, $sheet, $row, $column, $cellContent);
        } else {
            $this->processDomElementH1Etc($sheet, $row, $column, $cellContent, $child, $attributeArray);
        }
    }

    private static $h1Etc = ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'ol', 'ul', 'p'];

    private function processDomElementH1Etc(Worksheet $sheet, int &$row, string &$column, string &$cellContent, DOMElement $child, array &$attributeArray): void
    {
        if (in_array($child->nodeName, self::$h1Etc)) {
            if ($this->tableLevel > 0) {
                
                $cellContent .= $cellContent ? "\n" : '';
                $sheet->getStyle($column . $row)->getAlignment()->setWrapText(true);
                $this->processDomElement($child, $sheet, $row, $column, $cellContent);
            } else {
                if ($cellContent > '') {
                    $this->flushCell($sheet, $column, $row, $cellContent);
                    ++$row;
                }
                $this->processDomElement($child, $sheet, $row, $column, $cellContent);
                $this->flushCell($sheet, $column, $row, $cellContent);

                if (isset($this->formats[$child->nodeName])) {
                    $sheet->getStyle($column . $row)->applyFromArray($this->formats[$child->nodeName]);
                }

                ++$row;
                $column = 'A';
            }
        } else {
            $this->processDomElementLi($sheet, $row, $column, $cellContent, $child, $attributeArray);
        }
    }

    private function processDomElementLi(Worksheet $sheet, int &$row, string &$column, string &$cellContent, DOMElement $child, array &$attributeArray): void
    {
        if ($child->nodeName === 'li') {
            if ($this->tableLevel > 0) {
                
                $cellContent .= $cellContent ? "\n" : '';
                $this->processDomElement($child, $sheet, $row, $column, $cellContent);
            } else {
                if ($cellContent > '') {
                    $this->flushCell($sheet, $column, $row, $cellContent);
                }
                ++$row;
                $this->processDomElement($child, $sheet, $row, $column, $cellContent);
                $this->flushCell($sheet, $column, $row, $cellContent);
                $column = 'A';
            }
        } else {
            $this->processDomElementImg($sheet, $row, $column, $cellContent, $child, $attributeArray);
        }
    }

    private function processDomElementImg(Worksheet $sheet, int &$row, string &$column, string &$cellContent, DOMElement $child, array &$attributeArray): void
    {
        if ($child->nodeName === 'img') {
            $this->insertImage($sheet, $column, $row, $attributeArray);
        } else {
            $this->processDomElementTable($sheet, $row, $column, $cellContent, $child, $attributeArray);
        }
    }

    private function processDomElementTable(Worksheet $sheet, int &$row, string &$column, string &$cellContent, DOMElement $child, array &$attributeArray): void
    {
        if ($child->nodeName === 'table') {
            $this->flushCell($sheet, $column, $row, $cellContent);
            $column = $this->setTableStartColumn($column);
            if ($this->tableLevel > 1) {
                --$row;
            }
            $this->processDomElement($child, $sheet, $row, $column, $cellContent);
            $column = $this->releaseTableStartColumn();
            if ($this->tableLevel > 1) {
                ++$column;
            } else {
                ++$row;
            }
        } else {
            $this->processDomElementTr($sheet, $row, $column, $cellContent, $child, $attributeArray);
        }
    }

    private function processDomElementTr(Worksheet $sheet, int &$row, string &$column, string &$cellContent, DOMElement $child, array &$attributeArray): void
    {
        if ($child->nodeName === 'tr') {
            $column = $this->getTableStartColumn();
            $cellContent = '';
            $this->processDomElement($child, $sheet, $row, $column, $cellContent);

            if (isset($attributeArray['height'])) {
                $sheet->getRowDimension($row)->setRowHeight($attributeArray['height']);
            }

            ++$row;
        } else {
            $this->processDomElementThTdOther($sheet, $row, $column, $cellContent, $child, $attributeArray);
        }
    }

    private function processDomElementThTdOther(Worksheet $sheet, int &$row, string &$column, string &$cellContent, DOMElement $child, array &$attributeArray): void
    {
        if ($child->nodeName !== 'td' && $child->nodeName !== 'th') {
            $this->processDomElement($child, $sheet, $row, $column, $cellContent);
        } else {
            $this->processDomElementThTd($sheet, $row, $column, $cellContent, $child, $attributeArray);
        }
    }

    private function processDomElementBgcolor(Worksheet $sheet, int $row, string $column, array $attributeArray): void
    {
        if (isset($attributeArray['bgcolor'])) {
            $sheet->getStyle("$column$row")->applyFromArray(
                [
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color' => ['rgb' => $this->getStyleColor($attributeArray['bgcolor'])],
                    ],
                ]
            );
        }
    }

    private function processDomElementWidth(Worksheet $sheet, string $column, array $attributeArray): void
    {
        if (isset($attributeArray['width'])) {
            $sheet->getColumnDimension($column)->setWidth($attributeArray['width']);
        }
    }

    private function processDomElementHeight(Worksheet $sheet, int $row, array $attributeArray): void
    {
        if (isset($attributeArray['height'])) {
            $sheet->getRowDimension($row)->setRowHeight($attributeArray['height']);
        }
    }

    private function processDomElementAlign(Worksheet $sheet, int $row, string $column, array $attributeArray): void
    {
        if (isset($attributeArray['align'])) {
            $sheet->getStyle($column . $row)->getAlignment()->setHorizontal($attributeArray['align']);
        }
    }

    private function processDomElementVAlign(Worksheet $sheet, int $row, string $column, array $attributeArray): void
    {
        if (isset($attributeArray['valign'])) {
            $sheet->getStyle($column . $row)->getAlignment()->setVertical($attributeArray['valign']);
        }
    }

    private function processDomElementDataFormat(Worksheet $sheet, int $row, string $column, array $attributeArray): void
    {
        if (isset($attributeArray['data-format'])) {
            $sheet->getStyle($column . $row)->getNumberFormat()->setFormatCode($attributeArray['data-format']);
        }
    }

    private function processDomElementThTd(Worksheet $sheet, int &$row, string &$column, string &$cellContent, DOMElement $child, array &$attributeArray): void
    {
        while (isset($this->rowspan[$column . $row])) {
            ++$column;
        }
        $this->processDomElement($child, $sheet, $row, $column, $cellContent);

        
        $this->applyInlineStyle($sheet, $row, $column, $attributeArray);

        $this->flushCell($sheet, $column, $row, $cellContent);

        $this->processDomElementBgcolor($sheet, $row, $column, $attributeArray);
        $this->processDomElementWidth($sheet, $column, $attributeArray);
        $this->processDomElementHeight($sheet, $row, $attributeArray);
        $this->processDomElementAlign($sheet, $row, $column, $attributeArray);
        $this->processDomElementVAlign($sheet, $row, $column, $attributeArray);
        $this->processDomElementDataFormat($sheet, $row, $column, $attributeArray);

        if (isset($attributeArray['rowspan'], $attributeArray['colspan'])) {
            
            $columnTo = $column;
            for ($i = 0; $i < (int) $attributeArray['colspan'] - 1; ++$i) {
                ++$columnTo;
            }
            $range = $column . $row . ':' . $columnTo . ($row + (int) $attributeArray['rowspan'] - 1);
            foreach (Coordinate::extractAllCellReferencesInRange($range) as $value) {
                $this->rowspan[$value] = true;
            }
            $sheet->mergeCells($range);
            $column = $columnTo;
        } elseif (isset($attributeArray['rowspan'])) {
            
            $range = $column . $row . ':' . $column . ($row + (int) $attributeArray['rowspan'] - 1);
            foreach (Coordinate::extractAllCellReferencesInRange($range) as $value) {
                $this->rowspan[$value] = true;
            }
            $sheet->mergeCells($range);
        } elseif (isset($attributeArray['colspan'])) {
            
            $columnTo = $column;
            for ($i = 0; $i < (int) $attributeArray['colspan'] - 1; ++$i) {
                ++$columnTo;
            }
            $sheet->mergeCells($column . $row . ':' . $columnTo . $row);
            $column = $columnTo;
        }

        ++$column;
    }

    protected function processDomElement(DOMNode $element, Worksheet $sheet, int &$row, string &$column, string &$cellContent): void
    {
        foreach ($element->childNodes as $child) {
            if ($child instanceof DOMText) {
                $domText = preg_replace('/\s+/u', ' ', trim($child->nodeValue));
                if (is_string($cellContent)) {
                    
                    $cellContent .= $domText;
                }
                
                    
            } elseif ($child instanceof DOMElement) {
                $this->processDomElementBody($sheet, $row, $column, $cellContent, $child);
            }
        }
    }

    
    public function loadIntoExisting($pFilename, Spreadsheet $spreadsheet)
    {
        
        if (!$this->canRead($pFilename)) {
            throw new Exception($pFilename . ' is an Invalid HTML file.');
        }

        
        $dom = new DOMDocument();
        
        try {
            $loaded = $dom->loadHTML(mb_convert_encoding($this->securityScanner->scanFile($pFilename), 'HTML-ENTITIES', 'UTF-8'));
        } catch (Throwable $e) {
            $loaded = false;
        }
        if ($loaded === false) {
            throw new Exception('Failed to load ' . $pFilename . ' as a DOM Document');
        }

        return $this->loadDocument($dom, $spreadsheet);
    }

    
    public function loadFromString($content, ?Spreadsheet $spreadsheet = null): Spreadsheet
    {
        
        $dom = new DOMDocument();
        
        try {
            $loaded = $dom->loadHTML(mb_convert_encoding($this->securityScanner->scan($content), 'HTML-ENTITIES', 'UTF-8'));
        } catch (Throwable $e) {
            $loaded = false;
        }
        if ($loaded === false) {
            throw new Exception('Failed to load content as a DOM Document');
        }

        return $this->loadDocument($dom, $spreadsheet ?? new Spreadsheet());
    }

    
    private function loadDocument(DOMDocument $document, Spreadsheet $spreadsheet): Spreadsheet
    {
        while ($spreadsheet->getSheetCount() <= $this->sheetIndex) {
            $spreadsheet->createSheet();
        }
        $spreadsheet->setActiveSheetIndex($this->sheetIndex);

        
        $document->preserveWhiteSpace = false;

        $row = 0;
        $column = 'A';
        $content = '';
        $this->rowspan = [];
        $this->processDomElement($document, $spreadsheet->getActiveSheet(), $row, $column, $content);

        
        return $spreadsheet;
    }

    
    public function getSheetIndex()
    {
        return $this->sheetIndex;
    }

    
    public function setSheetIndex($pValue)
    {
        $this->sheetIndex = $pValue;

        return $this;
    }

    
    private function applyInlineStyle(&$sheet, $row, $column, $attributeArray): void
    {
        if (!isset($attributeArray['style'])) {
            return;
        }

        if (isset($attributeArray['rowspan'], $attributeArray['colspan'])) {
            $columnTo = $column;
            for ($i = 0; $i < (int) $attributeArray['colspan'] - 1; ++$i) {
                ++$columnTo;
            }
            $range = $column . $row . ':' . $columnTo . ($row + (int) $attributeArray['rowspan'] - 1);
            $cellStyle = $sheet->getStyle($range);
        } elseif (isset($attributeArray['rowspan'])) {
            $range = $column . $row . ':' . $column . ($row + (int) $attributeArray['rowspan'] - 1);
            $cellStyle = $sheet->getStyle($range);
        } elseif (isset($attributeArray['colspan'])) {
            $columnTo = $column;
            for ($i = 0; $i < (int) $attributeArray['colspan'] - 1; ++$i) {
                ++$columnTo;
            }
            $range = $column . $row . ':' . $columnTo . $row;
            $cellStyle = $sheet->getStyle($range);
        } else {
            $cellStyle = $sheet->getStyle($column . $row);
        }

        
        $styles = explode(';', $attributeArray['style']);
        foreach ($styles as $st) {
            $value = explode(':', $st);
            $styleName = isset($value[0]) ? trim($value[0]) : null;
            $styleValue = isset($value[1]) ? trim($value[1]) : null;

            if (!$styleName) {
                continue;
            }

            switch ($styleName) {
                case 'background':
                case 'background-color':
                    $styleColor = $this->getStyleColor($styleValue);

                    if (!$styleColor) {
                        continue 2;
                    }

                    $cellStyle->applyFromArray(['fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => $styleColor]]]);

                    break;
                case 'color':
                    $styleColor = $this->getStyleColor($styleValue);

                    if (!$styleColor) {
                        continue 2;
                    }

                    $cellStyle->applyFromArray(['font' => ['color' => ['rgb' => $styleColor]]]);

                    break;

                case 'border':
                    $this->setBorderStyle($cellStyle, $styleValue, 'allBorders');

                    break;

                case 'border-top':
                    $this->setBorderStyle($cellStyle, $styleValue, 'top');

                    break;

                case 'border-bottom':
                    $this->setBorderStyle($cellStyle, $styleValue, 'bottom');

                    break;

                case 'border-left':
                    $this->setBorderStyle($cellStyle, $styleValue, 'left');

                    break;

                case 'border-right':
                    $this->setBorderStyle($cellStyle, $styleValue, 'right');

                    break;

                case 'font-size':
                    $cellStyle->getFont()->setSize(
                        (float) $styleValue
                    );

                    break;

                case 'font-weight':
                    if ($styleValue === 'bold' || $styleValue >= 500) {
                        $cellStyle->getFont()->setBold(true);
                    }

                    break;

                case 'font-style':
                    if ($styleValue === 'italic') {
                        $cellStyle->getFont()->setItalic(true);
                    }

                    break;

                case 'font-family':
                    $cellStyle->getFont()->setName(str_replace('\'', '', $styleValue));

                    break;

                case 'text-decoration':
                    switch ($styleValue) {
                        case 'underline':
                            $cellStyle->getFont()->setUnderline(Font::UNDERLINE_SINGLE);

                            break;
                        case 'line-through':
                            $cellStyle->getFont()->setStrikethrough(true);

                            break;
                    }

                    break;

                case 'text-align':
                    $cellStyle->getAlignment()->setHorizontal($styleValue);

                    break;

                case 'vertical-align':
                    $cellStyle->getAlignment()->setVertical($styleValue);

                    break;

                case 'width':
                    $sheet->getColumnDimension($column)->setWidth(
                        str_replace('px', '', $styleValue)
                    );

                    break;

                case 'height':
                    $sheet->getRowDimension($row)->setRowHeight(
                        str_replace('px', '', $styleValue)
                    );

                    break;

                case 'word-wrap':
                    $cellStyle->getAlignment()->setWrapText(
                        $styleValue === 'break-word'
                    );

                    break;

                case 'text-indent':
                    $cellStyle->getAlignment()->setIndent(
                        (int) str_replace(['px'], '', $styleValue)
                    );

                    break;
            }
        }
    }

    
    public function getStyleColor($value)
    {
        if (strpos($value, '#') === 0) {
            return substr($value, 1);
        }

        return \PhpOffice\PhpSpreadsheet\Helper\Html::colourNameLookup((string) $value);
    }

    
    private function insertImage(Worksheet $sheet, $column, $row, array $attributes): void
    {
        if (!isset($attributes['src'])) {
            return;
        }

        $src = urldecode($attributes['src']);
        $width = isset($attributes['width']) ? (float) $attributes['width'] : null;
        $height = isset($attributes['height']) ? (float) $attributes['height'] : null;
        $name = $attributes['alt'] ?? null;

        $drawing = new Drawing();
        $drawing->setPath($src);
        $drawing->setWorksheet($sheet);
        $drawing->setCoordinates($column . $row);
        $drawing->setOffsetX(0);
        $drawing->setOffsetY(10);
        $drawing->setResizeProportional(true);

        if ($name) {
            $drawing->setName($name);
        }

        if ($width) {
            $drawing->setWidth((int) $width);
        }

        if ($height) {
            $drawing->setHeight((int) $height);
        }

        $sheet->getColumnDimension($column)->setWidth(
            $drawing->getWidth() / 6
        );

        $sheet->getRowDimension($row)->setRowHeight(
            $drawing->getHeight() * 0.9
        );
    }

    private static $borderMappings = [
        'dash-dot' => Border::BORDER_DASHDOT,
        'dash-dot-dot' => Border::BORDER_DASHDOTDOT,
        'dashed' => Border::BORDER_DASHED,
        'dotted' => Border::BORDER_DOTTED,
        'double' => Border::BORDER_DOUBLE,
        'hair' => Border::BORDER_HAIR,
        'medium' => Border::BORDER_MEDIUM,
        'medium-dashed' => Border::BORDER_MEDIUMDASHED,
        'medium-dash-dot' => Border::BORDER_MEDIUMDASHDOT,
        'medium-dash-dot-dot' => Border::BORDER_MEDIUMDASHDOTDOT,
        'none' => Border::BORDER_NONE,
        'slant-dash-dot' => Border::BORDER_SLANTDASHDOT,
        'solid' => Border::BORDER_THIN,
        'thick' => Border::BORDER_THICK,
    ];

    public static function getBorderMappings(): array
    {
        return self::$borderMappings;
    }

    
    public function getBorderStyle($style)
    {
        return (array_key_exists($style, self::$borderMappings)) ? self::$borderMappings[$style] : null;
    }

    
    private function setBorderStyle(Style $cellStyle, $styleValue, $type): void
    {
        if (trim($styleValue) === Border::BORDER_NONE) {
            $borderStyle = Border::BORDER_NONE;
            $color = null;
        } else {
            [, $borderStyle, $color] = explode(' ', $styleValue);
        }

        $cellStyle->applyFromArray([
            'borders' => [
                $type => [
                    'borderStyle' => $this->getBorderStyle($borderStyle),
                    'color' => ['rgb' => $this->getStyleColor($color)],
                ],
            ],
        ]);
    }
}
