<?php

namespace PhpOffice\PhpSpreadsheet\Style;

class Color extends Supervisor
{
    const NAMED_COLORS = [
        'Black',
        'White',
        'Red',
        'Green',
        'Blue',
        'Yellow',
        'Magenta',
        'Cyan',
    ];

    
    const COLOR_BLACK = 'FF000000';
    const COLOR_WHITE = 'FFFFFFFF';
    const COLOR_RED = 'FFFF0000';
    const COLOR_DARKRED = 'FF800000';
    const COLOR_BLUE = 'FF0000FF';
    const COLOR_DARKBLUE = 'FF000080';
    const COLOR_GREEN = 'FF00FF00';
    const COLOR_DARKGREEN = 'FF008000';
    const COLOR_YELLOW = 'FFFFFF00';
    const COLOR_DARKYELLOW = 'FF808000';

    
    protected static $indexedColors;

    
    protected $argb;

    
    public function __construct($pARGB = self::COLOR_BLACK, $isSupervisor = false, $isConditional = false)
    {
        
        parent::__construct($isSupervisor);

        
        if (!$isConditional) {
            $this->argb = $pARGB;
        }
    }

    
    public function getSharedComponent()
    {
        if ($this->parentPropertyName === 'endColor') {
            return $this->parent->getSharedComponent()->getEndColor();
        }
        if ($this->parentPropertyName === 'startColor') {
            return $this->parent->getSharedComponent()->getStartColor();
        }

        return $this->parent->getSharedComponent()->getColor();
    }

    
    public function getStyleArray($array)
    {
        return $this->parent->getStyleArray([$this->parentPropertyName => $array]);
    }

    
    public function applyFromArray(array $pStyles)
    {
        if ($this->isSupervisor) {
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($this->getStyleArray($pStyles));
        } else {
            if (isset($pStyles['rgb'])) {
                $this->setRGB($pStyles['rgb']);
            }
            if (isset($pStyles['argb'])) {
                $this->setARGB($pStyles['argb']);
            }
        }

        return $this;
    }

    
    public function getARGB()
    {
        if ($this->isSupervisor) {
            return $this->getSharedComponent()->getARGB();
        }

        return $this->argb;
    }

    
    public function setARGB($pValue)
    {
        if ($pValue == '') {
            $pValue = self::COLOR_BLACK;
        }
        if ($this->isSupervisor) {
            $styleArray = $this->getStyleArray(['argb' => $pValue]);
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
        } else {
            $this->argb = $pValue;
        }

        return $this;
    }

    
    public function getRGB()
    {
        if ($this->isSupervisor) {
            return $this->getSharedComponent()->getRGB();
        }

        return substr($this->argb, 2);
    }

    
    public function setRGB($pValue)
    {
        if ($pValue == '') {
            $pValue = '000000';
        }
        if ($this->isSupervisor) {
            $styleArray = $this->getStyleArray(['argb' => 'FF' . $pValue]);
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
        } else {
            $this->argb = 'FF' . $pValue;
        }

        return $this;
    }

    
    private static function getColourComponent($RGB, $offset, $hex = true)
    {
        $colour = substr($RGB, $offset, 2);

        return ($hex) ? $colour : hexdec($colour);
    }

    
    public static function getRed($RGB, $hex = true)
    {
        return self::getColourComponent($RGB, strlen($RGB) - 6, $hex);
    }

    
    public static function getGreen($RGB, $hex = true)
    {
        return self::getColourComponent($RGB, strlen($RGB) - 4, $hex);
    }

    
    public static function getBlue($RGB, $hex = true)
    {
        return self::getColourComponent($RGB, strlen($RGB) - 2, $hex);
    }

    
    public static function changeBrightness($hex, $adjustPercentage)
    {
        $rgba = (strlen($hex) === 8);
        $adjustPercentage = max(-1.0, min(1.0, $adjustPercentage));

        $red = self::getRed($hex, false);
        $green = self::getGreen($hex, false);
        $blue = self::getBlue($hex, false);
        if ($adjustPercentage > 0) {
            $red += (255 - $red) * $adjustPercentage;
            $green += (255 - $green) * $adjustPercentage;
            $blue += (255 - $blue) * $adjustPercentage;
        } else {
            $red += $red * $adjustPercentage;
            $green += $green * $adjustPercentage;
            $blue += $blue * $adjustPercentage;
        }

        $rgb = strtoupper(
            str_pad(dechex((int) $red), 2, '0', 0) .
            str_pad(dechex((int) $green), 2, '0', 0) .
            str_pad(dechex((int) $blue), 2, '0', 0)
        );

        return (($rgba) ? 'FF' : '') . $rgb;
    }

    
    public static function indexedColor($pIndex, $background = false)
    {
        
        $pIndex = (int) $pIndex;

        
        if (self::$indexedColors === null) {
            self::$indexedColors = [
                1 => 'FF000000', 
                2 => 'FFFFFFFF', 
                3 => 'FFFF0000', 
                4 => 'FF00FF00', 
                5 => 'FF0000FF', 
                6 => 'FFFFFF00', 
                7 => 'FFFF00FF', 
                8 => 'FF00FFFF', 
                9 => 'FF800000', 
                10 => 'FF008000', 
                11 => 'FF000080', 
                12 => 'FF808000', 
                13 => 'FF800080', 
                14 => 'FF008080', 
                15 => 'FFC0C0C0', 
                16 => 'FF808080', 
                17 => 'FF9999FF', 
                18 => 'FF993366', 
                19 => 'FFFFFFCC', 
                20 => 'FFCCFFFF', 
                21 => 'FF660066', 
                22 => 'FFFF8080', 
                23 => 'FF0066CC', 
                24 => 'FFCCCCFF', 
                25 => 'FF000080', 
                26 => 'FFFF00FF', 
                27 => 'FFFFFF00', 
                28 => 'FF00FFFF', 
                29 => 'FF800080', 
                30 => 'FF800000', 
                31 => 'FF008080', 
                32 => 'FF0000FF', 
                33 => 'FF00CCFF', 
                34 => 'FFCCFFFF', 
                35 => 'FFCCFFCC', 
                36 => 'FFFFFF99', 
                37 => 'FF99CCFF', 
                38 => 'FFFF99CC', 
                39 => 'FFCC99FF', 
                40 => 'FFFFCC99', 
                41 => 'FF3366FF', 
                42 => 'FF33CCCC', 
                43 => 'FF99CC00', 
                44 => 'FFFFCC00', 
                45 => 'FFFF9900', 
                46 => 'FFFF6600', 
                47 => 'FF666699', 
                48 => 'FF969696', 
                49 => 'FF003366', 
                50 => 'FF339966', 
                51 => 'FF003300', 
                52 => 'FF333300', 
                53 => 'FF993300', 
                54 => 'FF993366', 
                55 => 'FF333399', 
                56 => 'FF333333', 
            ];
        }

        if (isset(self::$indexedColors[$pIndex])) {
            return new self(self::$indexedColors[$pIndex]);
        }

        if ($background) {
            return new self(self::COLOR_WHITE);
        }

        return new self(self::COLOR_BLACK);
    }

    
    public function getHashCode()
    {
        if ($this->isSupervisor) {
            return $this->getSharedComponent()->getHashCode();
        }

        return md5(
            $this->argb .
            __CLASS__
        );
    }

    protected function exportArray1(): array
    {
        $exportedArray = [];
        $this->exportArray2($exportedArray, 'argb', $this->getARGB());

        return $exportedArray;
    }
}
