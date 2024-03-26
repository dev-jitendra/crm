<?php

namespace PhpOffice\PhpSpreadsheet\Writer;

use HTMLPurifier;
use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\RichText\Run;
use PhpOffice\PhpSpreadsheet\Shared\Drawing as SharedDrawing;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Shared\Font as SharedFont;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Borders;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class Html extends BaseWriter
{
    
    protected $spreadsheet;

    
    private $sheetIndex = 0;

    
    private $imagesRoot = '';

    
    private $embedImages = false;

    
    private $useInlineCss = false;

    
    private $useEmbeddedCSS = true;

    
    private $cssStyles;

    
    private $columnWidths;

    
    private $defaultFont;

    
    private $spansAreCalculated = false;

    
    private $isSpannedCell = [];

    
    private $isBaseCell = [];

    
    private $isSpannedRow = [];

    
    protected $isPdf = false;

    
    private $generateSheetNavigationBlock = true;

    
    private $editHtmlCallback;

    
    public function __construct(Spreadsheet $spreadsheet)
    {
        $this->spreadsheet = $spreadsheet;
        $this->defaultFont = $this->spreadsheet->getDefaultStyle()->getFont();
    }

    
    public function save($pFilename): void
    {
        
        $this->openFileHandle($pFilename);

        
        fwrite($this->fileHandle, $this->generateHTMLAll());

        
        $this->maybeCloseFileHandle();
    }

    
    public function generateHtmlAll()
    {
        
        $this->spreadsheet->garbageCollect();

        $saveDebugLog = Calculation::getInstance($this->spreadsheet)->getDebugLog()->getWriteDebugLog();
        Calculation::getInstance($this->spreadsheet)->getDebugLog()->setWriteDebugLog(false);
        $saveArrayReturnType = Calculation::getArrayReturnType();
        Calculation::setArrayReturnType(Calculation::RETURN_ARRAY_AS_VALUE);

        
        $this->buildCSS(!$this->useInlineCss);

        $html = '';

        
        $html .= $this->generateHTMLHeader(!$this->useInlineCss);

        
        if ((!$this->isPdf) && ($this->generateSheetNavigationBlock)) {
            $html .= $this->generateNavigation();
        }

        
        $html .= $this->generateSheetData();

        
        $html .= $this->generateHTMLFooter();
        $callback = $this->editHtmlCallback;
        if ($callback) {
            $html = $callback($html);
        }

        Calculation::setArrayReturnType($saveArrayReturnType);
        Calculation::getInstance($this->spreadsheet)->getDebugLog()->setWriteDebugLog($saveDebugLog);

        return $html;
    }

    
    public function setEditHtmlCallback(?callable $callback): void
    {
        $this->editHtmlCallback = $callback;
    }

    const VALIGN_ARR = [
        Alignment::VERTICAL_BOTTOM => 'bottom',
        Alignment::VERTICAL_TOP => 'top',
        Alignment::VERTICAL_CENTER => 'middle',
        Alignment::VERTICAL_JUSTIFY => 'middle',
    ];

    
    private function mapVAlign($vAlign)
    {
        return array_key_exists($vAlign, self::VALIGN_ARR) ? self::VALIGN_ARR[$vAlign] : 'baseline';
    }

    const HALIGN_ARR = [
        Alignment::HORIZONTAL_LEFT => 'left',
        Alignment::HORIZONTAL_RIGHT => 'right',
        Alignment::HORIZONTAL_CENTER => 'center',
        Alignment::HORIZONTAL_CENTER_CONTINUOUS => 'center',
        Alignment::HORIZONTAL_JUSTIFY => 'justify',
    ];

    
    private function mapHAlign($hAlign)
    {
        return array_key_exists($hAlign, self::HALIGN_ARR) ? self::HALIGN_ARR[$hAlign] : '';
    }

    const BORDER_ARR = [
        Border::BORDER_NONE => 'none',
        Border::BORDER_DASHDOT => '1px dashed',
        Border::BORDER_DASHDOTDOT => '1px dotted',
        Border::BORDER_DASHED => '1px dashed',
        Border::BORDER_DOTTED => '1px dotted',
        Border::BORDER_DOUBLE => '3px double',
        Border::BORDER_HAIR => '1px solid',
        Border::BORDER_MEDIUM => '2px solid',
        Border::BORDER_MEDIUMDASHDOT => '2px dashed',
        Border::BORDER_MEDIUMDASHDOTDOT => '2px dotted',
        Border::BORDER_SLANTDASHDOT => '2px dashed',
        Border::BORDER_THICK => '3px solid',
    ];

    
    private function mapBorderStyle($borderStyle)
    {
        return array_key_exists($borderStyle, self::BORDER_ARR) ? self::BORDER_ARR[$borderStyle] : '1px solid';
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

    
    public function getGenerateSheetNavigationBlock()
    {
        return $this->generateSheetNavigationBlock;
    }

    
    public function setGenerateSheetNavigationBlock($pValue)
    {
        $this->generateSheetNavigationBlock = (bool) $pValue;

        return $this;
    }

    
    public function writeAllSheets()
    {
        $this->sheetIndex = null;

        return $this;
    }

    private static function generateMeta($val, $desc)
    {
        return $val ? ('      <meta name="' . $desc . '" content="' . htmlspecialchars($val) . '" />' . PHP_EOL) : '';
    }

    
    public function generateHTMLHeader($pIncludeStyles = false)
    {
        
        $properties = $this->spreadsheet->getProperties();
        $html = '<!DOCTYPE html PUBLIC "-
        $html .= '<html xmlns="http:
        $html .= '  <head>' . PHP_EOL;
        $html .= '      <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />' . PHP_EOL;
        $html .= '      <meta name="generator" content="PhpSpreadsheet, https:
        $html .= '      <title>' . htmlspecialchars($properties->getTitle()) . '</title>' . PHP_EOL;
        $html .= self::generateMeta($properties->getCreator(), 'author');
        $html .= self::generateMeta($properties->getTitle(), 'title');
        $html .= self::generateMeta($properties->getDescription(), 'description');
        $html .= self::generateMeta($properties->getSubject(), 'subject');
        $html .= self::generateMeta($properties->getKeywords(), 'keywords');
        $html .= self::generateMeta($properties->getCategory(), 'category');
        $html .= self::generateMeta($properties->getCompany(), 'company');
        $html .= self::generateMeta($properties->getManager(), 'manager');

        $html .= $pIncludeStyles ? $this->generateStyles(true) : $this->generatePageDeclarations(true);

        $html .= '  </head>' . PHP_EOL;
        $html .= '' . PHP_EOL;
        $html .= '  <body>' . PHP_EOL;

        return $html;
    }

    private function generateSheetPrep()
    {
        
        $this->calculateSpans();

        
        if ($this->sheetIndex === null) {
            $sheets = $this->spreadsheet->getAllSheets();
        } else {
            $sheets = [$this->spreadsheet->getSheet($this->sheetIndex)];
        }

        return $sheets;
    }

    private function generateSheetStarts($sheet, $rowMin)
    {
        
        $tbodyStart = $rowMin;
        $theadStart = $theadEnd = 0; 
        if ($sheet->getPageSetup()->isRowsToRepeatAtTopSet()) {
            $rowsToRepeatAtTop = $sheet->getPageSetup()->getRowsToRepeatAtTop();

            
            if ($rowsToRepeatAtTop[0] == 1) {
                $theadStart = $rowsToRepeatAtTop[0];
                $theadEnd = $rowsToRepeatAtTop[1];
                $tbodyStart = $rowsToRepeatAtTop[1] + 1;
            }
        }

        return [$theadStart, $theadEnd, $tbodyStart];
    }

    private function generateSheetTags($row, $theadStart, $theadEnd, $tbodyStart)
    {
        
        $startTag = ($row == $theadStart) ? ('        <thead>' . PHP_EOL) : '';
        if (!$startTag) {
            $startTag = ($row == $tbodyStart) ? ('        <tbody>' . PHP_EOL) : '';
        }
        $endTag = ($row == $theadEnd) ? ('        </thead>' . PHP_EOL) : '';
        $cellType = ($row >= $tbodyStart) ? 'td' : 'th';

        return [$cellType, $startTag, $endTag];
    }

    
    public function generateSheetData()
    {
        $sheets = $this->generateSheetPrep();

        
        $html = '';

        
        $sheetId = 0;
        foreach ($sheets as $sheet) {
            
            $html .= $this->generateTableHeader($sheet);

            
            [$min, $max] = explode(':', $sheet->calculateWorksheetDataDimension());
            [$minCol, $minRow] = Coordinate::coordinateFromString($min);
            $minCol = Coordinate::columnIndexFromString($minCol);
            [$maxCol, $maxRow] = Coordinate::coordinateFromString($max);
            $maxCol = Coordinate::columnIndexFromString($maxCol);

            [$theadStart, $theadEnd, $tbodyStart] = $this->generateSheetStarts($sheet, $minRow);

            
            $row = $minRow - 1;
            while ($row++ < $maxRow) {
                [$cellType, $startTag, $endTag] = $this->generateSheetTags($row, $theadStart, $theadEnd, $tbodyStart);
                $html .= $startTag;

                
                if (!isset($this->isSpannedRow[$sheet->getParent()->getIndex($sheet)][$row])) {
                    
                    $rowData = [];
                    
                    $column = $minCol;
                    while ($column <= $maxCol) {
                        
                        if ($sheet->cellExistsByColumnAndRow($column, $row)) {
                            $rowData[$column] = Coordinate::stringFromColumnIndex($column) . $row;
                        } else {
                            $rowData[$column] = '';
                        }
                        ++$column;
                    }
                    $html .= $this->generateRow($sheet, $rowData, $row - 1, $cellType);
                }

                $html .= $endTag;
            }
            $html .= $this->extendRowsForChartsAndImages($sheet, $row);

            
            $html .= $this->generateTableFooter();
            
            if ($this->isPdf && $this->useInlineCss) {
                if ($this->sheetIndex === null && $sheetId + 1 < $this->spreadsheet->getSheetCount()) {
                    $html .= '<div style="page-break-before:always" ></div>';
                }
            }

            
            ++$sheetId;
        }

        return $html;
    }

    
    public function generateNavigation()
    {
        
        $sheets = [];
        if ($this->sheetIndex === null) {
            $sheets = $this->spreadsheet->getAllSheets();
        } else {
            $sheets[] = $this->spreadsheet->getSheet($this->sheetIndex);
        }

        
        $html = '';

        
        if (count($sheets) > 1) {
            
            $sheetId = 0;

            $html .= '<ul class="navigation">' . PHP_EOL;

            foreach ($sheets as $sheet) {
                $html .= '  <li class="sheet' . $sheetId . '"><a href="#sheet' . $sheetId . '">' . $sheet->getTitle() . '</a></li>' . PHP_EOL;
                ++$sheetId;
            }

            $html .= '</ul>' . PHP_EOL;
        }

        return $html;
    }

    
    private function extendRowsForCharts(Worksheet $pSheet, int $row)
    {
        $rowMax = $row;
        $colMax = 'A';
        $anyfound = false;
        if ($this->includeCharts) {
            foreach ($pSheet->getChartCollection() as $chart) {
                if ($chart instanceof Chart) {
                    $anyfound = true;
                    $chartCoordinates = $chart->getTopLeftPosition();
                    $chartTL = Coordinate::coordinateFromString($chartCoordinates['cell']);
                    $chartCol = Coordinate::columnIndexFromString($chartTL[0]);
                    if ($chartTL[1] > $rowMax) {
                        $rowMax = $chartTL[1];
                        if ($chartCol > Coordinate::columnIndexFromString($colMax)) {
                            $colMax = $chartTL[0];
                        }
                    }
                }
            }
        }

        return [$rowMax, $colMax, $anyfound];
    }

    private function extendRowsForChartsAndImages(Worksheet $pSheet, int $row): string
    {
        [$rowMax, $colMax, $anyfound] = $this->extendRowsForCharts($pSheet, $row);

        foreach ($pSheet->getDrawingCollection() as $drawing) {
            $anyfound = true;
            $imageTL = Coordinate::coordinateFromString($drawing->getCoordinates());
            $imageCol = Coordinate::columnIndexFromString($imageTL[0]);
            if ($imageTL[1] > $rowMax) {
                $rowMax = $imageTL[1];
                if ($imageCol > Coordinate::columnIndexFromString($colMax)) {
                    $colMax = $imageTL[0];
                }
            }
        }

        
        if ($row === $rowMax || !$anyfound) {
            return '';
        }

        $html = '';
        ++$colMax;
        ++$row;
        while ($row <= $rowMax) {
            $html .= '<tr>';
            for ($col = 'A'; $col != $colMax; ++$col) {
                $htmlx = $this->writeImageInCell($pSheet, $col . $row);
                $htmlx .= $this->includeCharts ? $this->writeChartInCell($pSheet, $col . $row) : '';
                if ($htmlx) {
                    $html .= "<td class='style0' style='position: relative;'>$htmlx</td>";
                } else {
                    $html .= "<td class='style0'></td>";
                }
            }
            ++$row;
            $html .= '</tr>' . PHP_EOL;
        }

        return $html;
    }

    
    public static function winFileToUrl($filename)
    {
        
        if (substr($filename, 1, 2) === ':\\') {
            $filename = 'file:
        }

        return $filename;
    }

    
    private function writeImageInCell(Worksheet $pSheet, $coordinates)
    {
        
        $html = '';

        
        foreach ($pSheet->getDrawingCollection() as $drawing) {
            if ($drawing->getCoordinates() != $coordinates) {
                continue;
            }
            $filedesc = $drawing->getDescription();
            $filedesc = $filedesc ? htmlspecialchars($filedesc, ENT_QUOTES) : 'Embedded image';
            if ($drawing instanceof Drawing) {
                $filename = $drawing->getPath();

                
                $filename = preg_replace('/^[.]/', '', $filename);

                
                $filename = $this->getImagesRoot() . $filename;

                
                $filename = preg_replace('@^[.]([^/])@', '$1', $filename);

                
                $filename = htmlspecialchars($filename);

                $html .= PHP_EOL;
                $imageData = self::winFileToUrl($filename);

                if ($this->embedImages && !$this->isPdf) {
                    $picture = @file_get_contents($filename);
                    if ($picture !== false) {
                        $imageDetails = getimagesize($filename);
                        
                        $base64 = base64_encode($picture);
                        $imageData = 'data:' . $imageDetails['mime'] . ';base64,' . $base64;
                    }
                }

                $html .= '<img style="position: absolute; z-index: 1; left: ' .
                    $drawing->getOffsetX() . 'px; top: ' . $drawing->getOffsetY() . 'px; width: ' .
                    $drawing->getWidth() . 'px; height: ' . $drawing->getHeight() . 'px;" src="' .
                    $imageData . '" alt="' . $filedesc . '" />';
            } elseif ($drawing instanceof MemoryDrawing) {
                ob_start(); 
                imagepng($drawing->getImageResource()); 
                $contents = ob_get_contents(); 
                ob_end_clean(); 

                $dataUri = 'data:image/jpeg;base64,' . base64_encode($contents);

                
                
                
                
                $html .= '<img alt="' . $filedesc . '" src="' . $dataUri . '" style="max-width:100%;width:' . $drawing->getWidth() . 'px;" />';
            }
        }

        return $html;
    }

    
    private function writeChartInCell(Worksheet $pSheet, $coordinates)
    {
        
        $html = '';

        
        foreach ($pSheet->getChartCollection() as $chart) {
            if ($chart instanceof Chart) {
                $chartCoordinates = $chart->getTopLeftPosition();
                if ($chartCoordinates['cell'] == $coordinates) {
                    $chartFileName = File::sysGetTempDir() . '/' . uniqid('', true) . '.png';
                    if (!$chart->render($chartFileName)) {
                        return;
                    }

                    $html .= PHP_EOL;
                    $imageDetails = getimagesize($chartFileName);
                    $filedesc = $chart->getTitle();
                    $filedesc = $filedesc ? self::getChartCaption($filedesc->getCaption()) : '';
                    $filedesc = $filedesc ? htmlspecialchars($filedesc, ENT_QUOTES) : 'Embedded chart';
                    if ($fp = fopen($chartFileName, 'rb', 0)) {
                        $picture = fread($fp, filesize($chartFileName));
                        fclose($fp);
                        
                        $base64 = base64_encode($picture);
                        $imageData = 'data:' . $imageDetails['mime'] . ';base64,' . $base64;

                        $html .= '<img style="position: absolute; z-index: 1; left: ' . $chartCoordinates['xOffset'] . 'px; top: ' . $chartCoordinates['yOffset'] . 'px; width: ' . $imageDetails[0] . 'px; height: ' . $imageDetails[1] . 'px;" src="' . $imageData . '" alt="' . $filedesc . '" />' . PHP_EOL;

                        unlink($chartFileName);
                    }
                }
            }
        }

        
        return $html;
    }

    
    private static function getChartCaption($cap)
    {
        return is_array($cap) ? implode(' ', $cap) : $cap;
    }

    
    public function generateStyles($generateSurroundingHTML = true)
    {
        
        $css = $this->buildCSS($generateSurroundingHTML);

        
        $html = '';

        
        if ($generateSurroundingHTML) {
            $html .= '    <style type="text/css">' . PHP_EOL;
            $html .= (array_key_exists('html', $css)) ? ('      html { ' . $this->assembleCSS($css['html']) . ' }' . PHP_EOL) : '';
        }

        
        foreach ($css as $styleName => $styleDefinition) {
            if ($styleName != 'html') {
                $html .= '      ' . $styleName . ' { ' . $this->assembleCSS($styleDefinition) . ' }' . PHP_EOL;
            }
        }
        $html .= $this->generatePageDeclarations(false);

        
        if ($generateSurroundingHTML) {
            $html .= '    </style>' . PHP_EOL;
        }

        
        return $html;
    }

    private function buildCssRowHeights(Worksheet $sheet, array &$css, int $sheetIndex): void
    {
        
        foreach ($sheet->getRowDimensions() as $rowDimension) {
            $row = $rowDimension->getRowIndex() - 1;

            
            $css['table.sheet' . $sheetIndex . ' tr.row' . $row] = [];

            if ($rowDimension->getRowHeight() != -1) {
                $pt_height = $rowDimension->getRowHeight();
                $css['table.sheet' . $sheetIndex . ' tr.row' . $row]['height'] = $pt_height . 'pt';
            }
            if ($rowDimension->getVisible() === false) {
                $css['table.sheet' . $sheetIndex . ' tr.row' . $row]['display'] = 'none';
                $css['table.sheet' . $sheetIndex . ' tr.row' . $row]['visibility'] = 'hidden';
            }
        }
    }

    private function buildCssPerSheet(Worksheet $sheet, array &$css): void
    {
        
        $sheetIndex = $sheet->getParent()->getIndex($sheet);

        
        
        $sheet->calculateColumnWidths();

        
        $highestColumnIndex = Coordinate::columnIndexFromString($sheet->getHighestColumn()) - 1;
        $column = -1;
        while ($column++ < $highestColumnIndex) {
            $this->columnWidths[$sheetIndex][$column] = 42; 
            $css['table.sheet' . $sheetIndex . ' col.col' . $column]['width'] = '42pt';
        }

        
        foreach ($sheet->getColumnDimensions() as $columnDimension) {
            $column = Coordinate::columnIndexFromString($columnDimension->getColumnIndex()) - 1;
            $width = SharedDrawing::cellDimensionToPixels($columnDimension->getWidth(), $this->defaultFont);
            $width = SharedDrawing::pixelsToPoints($width);
            if ($columnDimension->getVisible() === false) {
                $css['table.sheet' . $sheetIndex . ' .column' . $column]['display'] = 'none';
            }
            if ($width >= 0) {
                $this->columnWidths[$sheetIndex][$column] = $width;
                $css['table.sheet' . $sheetIndex . ' col.col' . $column]['width'] = $width . 'pt';
            }
        }

        
        $rowDimension = $sheet->getDefaultRowDimension();

        
        $css['table.sheet' . $sheetIndex . ' tr'] = [];

        if ($rowDimension->getRowHeight() == -1) {
            $pt_height = SharedFont::getDefaultRowHeightByFont($this->spreadsheet->getDefaultStyle()->getFont());
        } else {
            $pt_height = $rowDimension->getRowHeight();
        }
        $css['table.sheet' . $sheetIndex . ' tr']['height'] = $pt_height . 'pt';
        if ($rowDimension->getVisible() === false) {
            $css['table.sheet' . $sheetIndex . ' tr']['display'] = 'none';
            $css['table.sheet' . $sheetIndex . ' tr']['visibility'] = 'hidden';
        }

        $this->buildCssRowHeights($sheet, $css, $sheetIndex);
    }

    
    public function buildCSS($generateSurroundingHTML = true)
    {
        
        if ($this->cssStyles !== null) {
            return $this->cssStyles;
        }

        
        $this->calculateSpans();

        
        $css = [];

        
        if ($generateSurroundingHTML) {
            
            $css['html']['font-family'] = 'Calibri, Arial, Helvetica, sans-serif';
            $css['html']['font-size'] = '11pt';
            $css['html']['background-color'] = 'white';
        }

        
        $css['a.comment-indicator:hover + div.comment'] = [
            'background' => '#ffd',
            'position' => 'absolute',
            'display' => 'block',
            'border' => '1px solid black',
            'padding' => '0.5em',
        ];

        $css['a.comment-indicator'] = [
            'background' => 'red',
            'display' => 'inline-block',
            'border' => '1px solid black',
            'width' => '0.5em',
            'height' => '0.5em',
        ];

        $css['div.comment']['display'] = 'none';

        
        $css['table']['border-collapse'] = 'collapse';

        
        $css['.b']['text-align'] = 'center'; 

        
        $css['.e']['text-align'] = 'center'; 

        
        $css['.f']['text-align'] = 'right'; 

        
        $css['.inlineStr']['text-align'] = 'left'; 

        
        $css['.n']['text-align'] = 'right'; 

        
        $css['.s']['text-align'] = 'left'; 

        
        foreach ($this->spreadsheet->getCellXfCollection() as $index => $style) {
            $css['td.style' . $index] = $this->createCSSStyle($style);
            $css['th.style' . $index] = $this->createCSSStyle($style);
        }

        
        $sheets = [];
        if ($this->sheetIndex === null) {
            $sheets = $this->spreadsheet->getAllSheets();
        } else {
            $sheets[] = $this->spreadsheet->getSheet($this->sheetIndex);
        }

        
        foreach ($sheets as $sheet) {
            $this->buildCssPerSheet($sheet, $css);
        }

        
        if ($this->cssStyles === null) {
            $this->cssStyles = $css;
        }

        
        return $css;
    }

    
    private function createCSSStyle(Style $pStyle)
    {
        
        return array_merge(
            $this->createCSSStyleAlignment($pStyle->getAlignment()),
            $this->createCSSStyleBorders($pStyle->getBorders()),
            $this->createCSSStyleFont($pStyle->getFont()),
            $this->createCSSStyleFill($pStyle->getFill())
        );
    }

    
    private function createCSSStyleAlignment(Alignment $pStyle)
    {
        
        $css = [];

        
        $css['vertical-align'] = $this->mapVAlign($pStyle->getVertical());
        $textAlign = $this->mapHAlign($pStyle->getHorizontal());
        if ($textAlign) {
            $css['text-align'] = $textAlign;
            if (in_array($textAlign, ['left', 'right'])) {
                $css['padding-' . $textAlign] = (string) ((int) $pStyle->getIndent() * 9) . 'px';
            }
        }

        return $css;
    }

    
    private function createCSSStyleFont(Font $pStyle)
    {
        
        $css = [];

        
        if ($pStyle->getBold()) {
            $css['font-weight'] = 'bold';
        }
        if ($pStyle->getUnderline() != Font::UNDERLINE_NONE && $pStyle->getStrikethrough()) {
            $css['text-decoration'] = 'underline line-through';
        } elseif ($pStyle->getUnderline() != Font::UNDERLINE_NONE) {
            $css['text-decoration'] = 'underline';
        } elseif ($pStyle->getStrikethrough()) {
            $css['text-decoration'] = 'line-through';
        }
        if ($pStyle->getItalic()) {
            $css['font-style'] = 'italic';
        }

        $css['color'] = '#' . $pStyle->getColor()->getRGB();
        $css['font-family'] = '\'' . $pStyle->getName() . '\'';
        $css['font-size'] = $pStyle->getSize() . 'pt';

        return $css;
    }

    
    private function createCSSStyleBorders(Borders $pStyle)
    {
        
        $css = [];

        
        $css['border-bottom'] = $this->createCSSStyleBorder($pStyle->getBottom());
        $css['border-top'] = $this->createCSSStyleBorder($pStyle->getTop());
        $css['border-left'] = $this->createCSSStyleBorder($pStyle->getLeft());
        $css['border-right'] = $this->createCSSStyleBorder($pStyle->getRight());

        return $css;
    }

    
    private function createCSSStyleBorder(Border $pStyle)
    {
        
        $borderStyle = $this->mapBorderStyle($pStyle->getBorderStyle());

        return $borderStyle . ' #' . $pStyle->getColor()->getRGB() . (($borderStyle == 'none') ? '' : ' !important');
    }

    
    private function createCSSStyleFill(Fill $pStyle)
    {
        
        $css = [];

        
        $value = $pStyle->getFillType() == Fill::FILL_NONE ?
            'white' : '#' . $pStyle->getStartColor()->getRGB();
        $css['background-color'] = $value;

        return $css;
    }

    
    public function generateHTMLFooter()
    {
        
        $html = '';
        $html .= '  </body>' . PHP_EOL;
        $html .= '</html>' . PHP_EOL;

        return $html;
    }

    private function generateTableTagInline($pSheet, $id)
    {
        $style = isset($this->cssStyles['table']) ?
            $this->assembleCSS($this->cssStyles['table']) : '';

        $prntgrid = $pSheet->getPrintGridlines();
        $viewgrid = $this->isPdf ? $prntgrid : $pSheet->getShowGridlines();
        if ($viewgrid && $prntgrid) {
            $html = "    <table border='1' cellpadding='1' $id cellspacing='1' style='$style' class='gridlines gridlinesp'>" . PHP_EOL;
        } elseif ($viewgrid) {
            $html = "    <table border='0' cellpadding='0' $id cellspacing='0' style='$style' class='gridlines'>" . PHP_EOL;
        } elseif ($prntgrid) {
            $html = "    <table border='0' cellpadding='0' $id cellspacing='0' style='$style' class='gridlinesp'>" . PHP_EOL;
        } else {
            $html = "    <table border='0' cellpadding='1' $id cellspacing='0' style='$style'>" . PHP_EOL;
        }

        return $html;
    }

    private function generateTableTag($pSheet, $id, &$html, $sheetIndex): void
    {
        if (!$this->useInlineCss) {
            $gridlines = $pSheet->getShowGridlines() ? ' gridlines' : '';
            $gridlinesp = $pSheet->getPrintGridlines() ? ' gridlinesp' : '';
            $html .= "    <table border='0' cellpadding='0' cellspacing='0' $id class='sheet$sheetIndex$gridlines$gridlinesp'>" . PHP_EOL;
        } else {
            $html .= $this->generateTableTagInline($pSheet, $id);
        }
    }

    
    private function generateTableHeader($pSheet, $showid = true)
    {
        $sheetIndex = $pSheet->getParent()->getIndex($pSheet);

        
        $html = '';
        $id = $showid ? "id='sheet$sheetIndex'" : '';
        if ($showid) {
            $html .= "<div style='page: page$sheetIndex'>\n";
        } else {
            $html .= "<div style='page: page$sheetIndex' class='scrpgbrk'>\n";
        }

        $this->generateTableTag($pSheet, $id, $html, $sheetIndex);

        
        $highestColumnIndex = Coordinate::columnIndexFromString($pSheet->getHighestColumn()) - 1;
        $i = -1;
        while ($i++ < $highestColumnIndex) {
            if (!$this->useInlineCss) {
                $html .= '        <col class="col' . $i . '" />' . PHP_EOL;
            } else {
                $style = isset($this->cssStyles['table.sheet' . $sheetIndex . ' col.col' . $i]) ?
                    $this->assembleCSS($this->cssStyles['table.sheet' . $sheetIndex . ' col.col' . $i]) : '';
                $html .= '        <col style="' . $style . '" />' . PHP_EOL;
            }
        }

        return $html;
    }

    
    private function generateTableFooter()
    {
        return '    </tbody></table>' . PHP_EOL . '</div>' . PHP_EOL;
    }

    
    private function generateRowStart(Worksheet $pSheet, $sheetIndex, $pRow)
    {
        $html = '';
        if (count($pSheet->getBreaks()) > 0) {
            $breaks = $pSheet->getBreaks();

            
            if (isset($breaks['A' . $pRow])) {
                
                $html .= $this->generateTableFooter();
                if ($this->isPdf && $this->useInlineCss) {
                    $html .= '<div style="page-break-before:always" />';
                }

                
                $html .= $this->generateTableHeader($pSheet, false);
                $html .= '<tbody>' . PHP_EOL;
            }
        }

        
        if (!$this->useInlineCss) {
            $html .= '          <tr class="row' . $pRow . '">' . PHP_EOL;
        } else {
            $style = isset($this->cssStyles['table.sheet' . $sheetIndex . ' tr.row' . $pRow])
                ? $this->assembleCSS($this->cssStyles['table.sheet' . $sheetIndex . ' tr.row' . $pRow]) : '';

            $html .= '          <tr style="' . $style . '">' . PHP_EOL;
        }

        return $html;
    }

    private function generateRowCellCss($pSheet, $cellAddress, $pRow, $colNum)
    {
        $cell = ($cellAddress > '') ? $pSheet->getCell($cellAddress) : '';
        $coordinate = Coordinate::stringFromColumnIndex($colNum + 1) . ($pRow + 1);
        if (!$this->useInlineCss) {
            $cssClass = 'column' . $colNum;
        } else {
            $cssClass = [];
            
            
            
            
            
            
            
            
            
            
            
            
            
        }

        return [$cell, $cssClass, $coordinate];
    }

    private function generateRowCellDataValueRich($cell, &$cellData): void
    {
        
        $elements = $cell->getValue()->getRichTextElements();
        foreach ($elements as $element) {
            
            if ($element instanceof Run) {
                $cellData .= '<span style="' . $this->assembleCSS($this->createCSSStyleFont($element->getFont())) . '">';

                $cellEnd = '';
                if ($element->getFont()->getSuperscript()) {
                    $cellData .= '<sup>';
                    $cellEnd = '</sup>';
                } elseif ($element->getFont()->getSubscript()) {
                    $cellData .= '<sub>';
                    $cellEnd = '</sub>';
                }

                
                $cellText = $element->getText();
                $cellData .= htmlspecialchars($cellText);

                $cellData .= $cellEnd;

                $cellData .= '</span>';
            } else {
                
                $cellText = $element->getText();
                $cellData .= htmlspecialchars($cellText);
            }
        }
    }

    private function generateRowCellDataValue($pSheet, $cell, &$cellData): void
    {
        if ($cell->getValue() instanceof RichText) {
            $this->generateRowCellDataValueRich($cell, $cellData);
        } else {
            $origData = $this->preCalculateFormulas ? $cell->getCalculatedValue() : $cell->getValue();
            $cellData = NumberFormat::toFormattedString(
                $origData,
                $pSheet->getParent()->getCellXfByIndex($cell->getXfIndex())->getNumberFormat()->getFormatCode(),
                [$this, 'formatColor']
            );
            if ($cellData === $origData) {
                $cellData = htmlspecialchars($cellData);
            }
            if ($pSheet->getParent()->getCellXfByIndex($cell->getXfIndex())->getFont()->getSuperscript()) {
                $cellData = '<sup>' . $cellData . '</sup>';
            } elseif ($pSheet->getParent()->getCellXfByIndex($cell->getXfIndex())->getFont()->getSubscript()) {
                $cellData = '<sub>' . $cellData . '</sub>';
            }
        }
    }

    private function generateRowCellData($pSheet, $cell, &$cssClass, $cellType)
    {
        $cellData = '&nbsp;';
        if ($cell instanceof Cell) {
            $cellData = '';
            
            
            
            
            
            $this->generateRowCellDataValue($pSheet, $cell, $cellData);

            
            
            $cellData = preg_replace('/(?m)(?:^|\\G) /', '&nbsp;', $cellData);

            
            $cellData = nl2br($cellData);

            
            if (!$this->useInlineCss) {
                $cssClass .= ' style' . $cell->getXfIndex();
                $cssClass .= ' ' . $cell->getDataType();
            } else {
                if ($cellType == 'th') {
                    if (isset($this->cssStyles['th.style' . $cell->getXfIndex()])) {
                        $cssClass = array_merge($cssClass, $this->cssStyles['th.style' . $cell->getXfIndex()]);
                    }
                } else {
                    if (isset($this->cssStyles['td.style' . $cell->getXfIndex()])) {
                        $cssClass = array_merge($cssClass, $this->cssStyles['td.style' . $cell->getXfIndex()]);
                    }
                }

                
                $sharedStyle = $pSheet->getParent()->getCellXfByIndex($cell->getXfIndex());
                if (
                    $sharedStyle->getAlignment()->getHorizontal() == Alignment::HORIZONTAL_GENERAL
                    && isset($this->cssStyles['.' . $cell->getDataType()]['text-align'])
                ) {
                    $cssClass['text-align'] = $this->cssStyles['.' . $cell->getDataType()]['text-align'];
                }
            }
        } else {
            
            if (is_string($cssClass)) {
                $cssClass .= ' style0';
            }
        }

        return $cellData;
    }

    private function generateRowIncludeCharts($pSheet, $coordinate)
    {
        return $this->includeCharts ? $this->writeChartInCell($pSheet, $coordinate) : '';
    }

    private function generateRowSpans($html, $rowSpan, $colSpan)
    {
        $html .= ($colSpan > 1) ? (' colspan="' . $colSpan . '"') : '';
        $html .= ($rowSpan > 1) ? (' rowspan="' . $rowSpan . '"') : '';

        return $html;
    }

    private function generateRowWriteCell(&$html, $pSheet, $coordinate, $cellType, $cellData, $colSpan, $rowSpan, $cssClass, $colNum, $sheetIndex, $pRow): void
    {
        
        $htmlx = $this->writeImageInCell($pSheet, $coordinate);
        
        $htmlx .= $this->generateRowIncludeCharts($pSheet, $coordinate);
        
        $html .= '            <' . $cellType;
        if (!$this->useInlineCss && !$this->isPdf) {
            $html .= ' class="' . $cssClass . '"';
            if ($htmlx) {
                $html .= " style='position: relative;'";
            }
        } else {
            
            
            
            if ($this->useInlineCss) {
                $xcssClass = $cssClass;
            } else {
                $html .= ' class="' . $cssClass . '"';
                $xcssClass = [];
            }
            $width = 0;
            $i = $colNum - 1;
            $e = $colNum + $colSpan - 1;
            while ($i++ < $e) {
                if (isset($this->columnWidths[$sheetIndex][$i])) {
                    $width += $this->columnWidths[$sheetIndex][$i];
                }
            }
            $xcssClass['width'] = $width . 'pt';

            
            
            if (isset($this->cssStyles['table.sheet' . $sheetIndex . ' tr.row' . $pRow]['height'])) {
                $height = $this->cssStyles['table.sheet' . $sheetIndex . ' tr.row' . $pRow]['height'];
                $xcssClass['height'] = $height;
            }
            

            if ($htmlx) {
                $xcssClass['position'] = 'relative';
            }
            $html .= ' style="' . $this->assembleCSS($xcssClass) . '"';
        }
        $html = $this->generateRowSpans($html, $rowSpan, $colSpan);

        $html .= '>';
        $html .= $htmlx;

        $html .= $this->writeComment($pSheet, $coordinate);

        
        $html .= $cellData;

        
        $html .= '</' . $cellType . '>' . PHP_EOL;
    }

    
    private function generateRow(Worksheet $pSheet, array $pValues, $pRow, $cellType)
    {
        
        $sheetIndex = $pSheet->getParent()->getIndex($pSheet);
        $html = $this->generateRowStart($pSheet, $sheetIndex, $pRow);

        
        $colNum = 0;
        foreach ($pValues as $cellAddress) {
            [$cell, $cssClass, $coordinate] = $this->generateRowCellCss($pSheet, $cellAddress, $pRow, $colNum);

            $colSpan = 1;
            $rowSpan = 1;

            
            $cellData = $this->generateRowCellData($pSheet, $cell, $cssClass, $cellType);

            
            if ($pSheet->hyperlinkExists($coordinate) && !$pSheet->getHyperlink($coordinate)->isInternal()) {
                $cellData = '<a href="' . htmlspecialchars($pSheet->getHyperlink($coordinate)->getUrl()) . '" title="' . htmlspecialchars($pSheet->getHyperlink($coordinate)->getTooltip()) . '">' . $cellData . '</a>';
            }

            
            $writeCell = !(isset($this->isSpannedCell[$pSheet->getParent()->getIndex($pSheet)][$pRow + 1][$colNum])
                && $this->isSpannedCell[$pSheet->getParent()->getIndex($pSheet)][$pRow + 1][$colNum]);

            
            $colspan = 1;
            $rowspan = 1;
            if (isset($this->isBaseCell[$pSheet->getParent()->getIndex($pSheet)][$pRow + 1][$colNum])) {
                $spans = $this->isBaseCell[$pSheet->getParent()->getIndex($pSheet)][$pRow + 1][$colNum];
                $rowSpan = $spans['rowspan'];
                $colSpan = $spans['colspan'];

                
                
                $endCellCoord = Coordinate::stringFromColumnIndex($colNum + $colSpan) . ($pRow + $rowSpan);
                if (!$this->useInlineCss) {
                    $cssClass .= ' style' . $pSheet->getCell($endCellCoord)->getXfIndex();
                }
            }

            
            if ($writeCell) {
                $this->generateRowWriteCell($html, $pSheet, $coordinate, $cellType, $cellData, $colSpan, $rowSpan, $cssClass, $colNum, $sheetIndex, $pRow);
            }

            
            ++$colNum;
        }

        
        $html .= '          </tr>' . PHP_EOL;

        
        return $html;
    }

    
    private function assembleCSS(array $pValue = [])
    {
        $pairs = [];
        foreach ($pValue as $property => $value) {
            $pairs[] = $property . ':' . $value;
        }
        $string = implode('; ', $pairs);

        return $string;
    }

    
    public function getImagesRoot()
    {
        return $this->imagesRoot;
    }

    
    public function setImagesRoot($pValue)
    {
        $this->imagesRoot = $pValue;

        return $this;
    }

    
    public function getEmbedImages()
    {
        return $this->embedImages;
    }

    
    public function setEmbedImages($pValue)
    {
        $this->embedImages = $pValue;

        return $this;
    }

    
    public function getUseInlineCss()
    {
        return $this->useInlineCss;
    }

    
    public function setUseInlineCss($pValue)
    {
        $this->useInlineCss = $pValue;

        return $this;
    }

    
    public function getUseEmbeddedCSS()
    {
        return $this->useEmbeddedCSS;
    }

    
    public function setUseEmbeddedCSS($pValue)
    {
        $this->useEmbeddedCSS = $pValue;

        return $this;
    }

    
    public function formatColor($pValue, $pFormat)
    {
        
        $color = null; 
        $matches = [];

        $color_regex = '/^\\[[a-zA-Z]+\\]/';
        if (preg_match($color_regex, $pFormat, $matches)) {
            $color = str_replace(['[', ']'], '', $matches[0]);
            $color = strtolower($color);
        }

        
        $value = htmlspecialchars($pValue);

        
        if ($color !== null) {
            $value = '<span style="color:' . $color . '">' . $value . '</span>';
        }

        return $value;
    }

    
    private function calculateSpans(): void
    {
        if ($this->spansAreCalculated) {
            return;
        }
        
        
        
        $sheetIndexes = $this->sheetIndex !== null ?
            [$this->sheetIndex] : range(0, $this->spreadsheet->getSheetCount() - 1);

        foreach ($sheetIndexes as $sheetIndex) {
            $sheet = $this->spreadsheet->getSheet($sheetIndex);

            $candidateSpannedRow = [];

            
            foreach ($sheet->getMergeCells() as $cells) {
                [$cells] = Coordinate::splitRange($cells);
                $first = $cells[0];
                $last = $cells[1];

                [$fc, $fr] = Coordinate::coordinateFromString($first);
                $fc = Coordinate::columnIndexFromString($fc) - 1;

                [$lc, $lr] = Coordinate::coordinateFromString($last);
                $lc = Coordinate::columnIndexFromString($lc) - 1;

                
                $r = $fr - 1;
                while ($r++ < $lr) {
                    
                    $candidateSpannedRow[$r] = $r;

                    $c = $fc - 1;
                    while ($c++ < $lc) {
                        if (!($c == $fc && $r == $fr)) {
                            
                            $this->isSpannedCell[$sheetIndex][$r][$c] = [
                                'baseCell' => [$fr, $fc],
                            ];
                        } else {
                            
                            $this->isBaseCell[$sheetIndex][$r][$c] = [
                                'xlrowspan' => $lr - $fr + 1, 
                                'rowspan' => $lr - $fr + 1, 
                                'xlcolspan' => $lc - $fc + 1, 
                                'colspan' => $lc - $fc + 1, 
                            ];
                        }
                    }
                }
            }

            $this->calculateSpansOmitRows($sheet, $sheetIndex, $candidateSpannedRow);

            
        }

        
        $this->spansAreCalculated = true;
    }

    private function calculateSpansOmitRows($sheet, $sheetIndex, $candidateSpannedRow): void
    {
        
        
        $countColumns = Coordinate::columnIndexFromString($sheet->getHighestColumn());
        foreach ($candidateSpannedRow as $rowIndex) {
            if (isset($this->isSpannedCell[$sheetIndex][$rowIndex])) {
                if (count($this->isSpannedCell[$sheetIndex][$rowIndex]) == $countColumns) {
                    $this->isSpannedRow[$sheetIndex][$rowIndex] = $rowIndex;
                }
            }
        }

        
        if (isset($this->isSpannedRow[$sheetIndex])) {
            foreach ($this->isSpannedRow[$sheetIndex] as $rowIndex) {
                $adjustedBaseCells = [];
                $c = -1;
                $e = $countColumns - 1;
                while ($c++ < $e) {
                    $baseCell = $this->isSpannedCell[$sheetIndex][$rowIndex][$c]['baseCell'];

                    if (!in_array($baseCell, $adjustedBaseCells)) {
                        
                        --$this->isBaseCell[$sheetIndex][$baseCell[0]][$baseCell[1]]['rowspan'];
                        $adjustedBaseCells[] = $baseCell;
                    }
                }
            }
        }
    }

    
    private function writeComment(Worksheet $pSheet, $coordinate)
    {
        $result = '';
        if (!$this->isPdf && isset($pSheet->getComments()[$coordinate])) {
            $sanitizer = new HTMLPurifier();
            $sanitizedString = $sanitizer->purify($pSheet->getComment($coordinate)->getText()->getPlainText());
            if ($sanitizedString !== '') {
                $result .= '<a class="comment-indicator"></a>';
                $result .= '<div class="comment">' . nl2br($sanitizedString) . '</div>';
                $result .= PHP_EOL;
            }
        }

        return $result;
    }

    
    private function generatePageDeclarations($generateSurroundingHTML)
    {
        
        $this->calculateSpans();

        
        $sheets = [];
        if ($this->sheetIndex === null) {
            $sheets = $this->spreadsheet->getAllSheets();
        } else {
            $sheets[] = $this->spreadsheet->getSheet($this->sheetIndex);
        }

        
        $htmlPage = $generateSurroundingHTML ? ('<style type="text/css">' . PHP_EOL) : '';

        
        $sheetId = 0;
        foreach ($sheets as $pSheet) {
            $htmlPage .= "@page page$sheetId { ";
            $left = StringHelper::formatNumber($pSheet->getPageMargins()->getLeft()) . 'in; ';
            $htmlPage .= 'margin-left: ' . $left;
            $right = StringHelper::FormatNumber($pSheet->getPageMargins()->getRight()) . 'in; ';
            $htmlPage .= 'margin-right: ' . $right;
            $top = StringHelper::FormatNumber($pSheet->getPageMargins()->getTop()) . 'in; ';
            $htmlPage .= 'margin-top: ' . $top;
            $bottom = StringHelper::FormatNumber($pSheet->getPageMargins()->getBottom()) . 'in; ';
            $htmlPage .= 'margin-bottom: ' . $bottom;
            $orientation = $pSheet->getPageSetup()->getOrientation();
            if ($orientation === \PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE) {
                $htmlPage .= 'size: landscape; ';
            } elseif ($orientation === \PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT) {
                $htmlPage .= 'size: portrait; ';
            }
            $htmlPage .= "}\n";
            ++$sheetId;
        }
        $htmlPage .= <<<EOF
.navigation {page-break-after: always;}
.scrpgbrk, div + div {page-break-before: always;}
@media screen {
  .gridlines td {border: 1px solid black;}
  .gridlines th {border: 1px solid black;}
  body>div {margin-top: 5px;}
  body>div:first-child {margin-top: 0;}
  .scrpgbrk {margin-top: 1px;}
}
@media print {
  .gridlinesp td {border: 1px solid black;}
  .gridlinesp th {border: 1px solid black;}
  .navigation {display: none;}
}

EOF;
        $htmlPage .= $generateSurroundingHTML ? ('</style>' . PHP_EOL) : '';

        return $htmlPage;
    }
}
