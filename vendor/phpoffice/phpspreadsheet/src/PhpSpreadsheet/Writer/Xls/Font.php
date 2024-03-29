<?php

namespace PhpOffice\PhpSpreadsheet\Writer\Xls;

use PhpOffice\PhpSpreadsheet\Shared\StringHelper;

class Font
{
    
    private $colorIndex;

    
    private $font;

    
    public function __construct(\PhpOffice\PhpSpreadsheet\Style\Font $font)
    {
        $this->colorIndex = 0x7FFF;
        $this->font = $font;
    }

    
    public function setColorIndex($colorIndex): void
    {
        $this->colorIndex = $colorIndex;
    }

    
    public function writeFont()
    {
        $font_outline = 0;
        $font_shadow = 0;

        $icv = $this->colorIndex; 
        if ($this->font->getSuperscript()) {
            $sss = 1;
        } elseif ($this->font->getSubscript()) {
            $sss = 2;
        } else {
            $sss = 0;
        }
        $bFamily = 0; 
        $bCharSet = \PhpOffice\PhpSpreadsheet\Shared\Font::getCharsetFromFontName($this->font->getName()); 

        $record = 0x31; 
        $reserved = 0x00; 
        $grbit = 0x00; 
        if ($this->font->getItalic()) {
            $grbit |= 0x02;
        }
        if ($this->font->getStrikethrough()) {
            $grbit |= 0x08;
        }
        if ($font_outline) {
            $grbit |= 0x10;
        }
        if ($font_shadow) {
            $grbit |= 0x20;
        }

        $data = pack(
            'vvvvvCCCC',
            
            $this->font->getSize() * 20,
            $grbit,
            
            $icv,
            
            self::mapBold($this->font->getBold()),
            
            $sss,
            self::mapUnderline($this->font->getUnderline()),
            $bFamily,
            $bCharSet,
            $reserved
        );
        $data .= StringHelper::UTF8toBIFF8UnicodeShort($this->font->getName());

        $length = strlen($data);
        $header = pack('vv', $record, $length);

        return $header . $data;
    }

    
    private static function mapBold($bold)
    {
        if ($bold) {
            return 0x2BC; 
        }

        return 0x190; 
    }

    
    private static $mapUnderline = [
        \PhpOffice\PhpSpreadsheet\Style\Font::UNDERLINE_NONE => 0x00,
        \PhpOffice\PhpSpreadsheet\Style\Font::UNDERLINE_SINGLE => 0x01,
        \PhpOffice\PhpSpreadsheet\Style\Font::UNDERLINE_DOUBLE => 0x02,
        \PhpOffice\PhpSpreadsheet\Style\Font::UNDERLINE_SINGLEACCOUNTING => 0x21,
        \PhpOffice\PhpSpreadsheet\Style\Font::UNDERLINE_DOUBLEACCOUNTING => 0x22,
    ];

    
    private static function mapUnderline($underline)
    {
        if (isset(self::$mapUnderline[$underline])) {
            return self::$mapUnderline[$underline];
        }

        return 0x00;
    }
}
