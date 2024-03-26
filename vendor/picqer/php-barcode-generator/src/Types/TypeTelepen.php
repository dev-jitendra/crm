<?php


namespace Picqer\Barcode\Types;

use Picqer\Barcode\Barcode;
use Picqer\Barcode\BarcodeBar;
use Picqer\Barcode\Exceptions\InvalidFormatException;

class TypeTelepen implements TypeInterface
{
    private const TELEPEN_START_CHAR = '_';
    private const TELEPEN_STOP_CHAR = 'z';
    private const TELEPEN_ALPHA = 'alpha';
    private const TELEPEN_NUMERIC = 'numeric';

    private $telepen_lookup_table;
    private $mode;

    public function __construct($m = 'alpha')
    {
        $this->mode = self::TELEPEN_ALPHA;
        if (strtolower($m) == 'numeric') {
            $this->mode = self::TELEPEN_NUMERIC;
        }
        $this->createTelepenConversionTable();
    }

    public function getBarcodeData(string $code): Barcode
    {
        

        $encoded = $this->encode($code); 
        $barcode = new Barcode($code);

        $drawBar = true;
        for ($i = 0; $i < strlen($encoded); ++$i) {
            $barWidth = $encoded[$i];
            $barcode->addBar(new BarcodeBar($barWidth, 250, $drawBar));
            $drawBar = !$drawBar; 
        }

        return $barcode;
    }

    protected function encode($code) : string
    {
        $result = null;
        if ($this->mode == self::TELEPEN_ALPHA) {
            $result = $this->encodeAlpha($code);
        } else {
            $result = $this->encodeNumeric($code);
        }

        return $result;
    }

    protected function encodeAlpha($code) : string
    {
        
        if (
            !preg_match('/[ -~]+/', $code)
        ) { 
            throw new InvalidFormatException("Invalid characters in data");
        }

        $count = 0;

        

        
        $dest = $this->telepen_lookup_table[ord(self::TELEPEN_START_CHAR)];

        for ($i = 0; $i < strlen($code); $i++) {
            
            $ascii_code = ord($code[$i]);
            $dest .= ($this->telepen_lookup_table[$ascii_code]);
            $count += $ascii_code;
        }

        
        $check_digit = 127 - ($count % 127);
        if ($check_digit == 127) {
            $check_digit = 0;
        }

        $dest .= $this->telepen_lookup_table[ord($check_digit)];
        $dest .= $this->telepen_lookup_table[ord(self::TELEPEN_STOP_CHAR)]; 

        return $dest;
    }

    private function encodeNumeric(string $code) : string
    {

        
        if (!preg_match('/^[0-9X]+$/', $code)) {
            throw new InvalidFormatException("Invalid characters in data");
        }

        
        $t = '';
        if (strlen($code) % 2 > 0) {
            throw new InvalidFormatException("There must be an even number of digits");
        }

        $count = 0;
        $dest = $this->telepen_lookup_table[ord(self::TELEPEN_START_CHAR)]; 
        
        for ($i = 0; $i < strlen($code); $i += 2) {
            $c1 = $code[$i];
            $c2 = $code[$i+1];
            
            if ($c1 == 'X') {
                throw new InvalidFormatException("Invalid position of X in data");
            }
            $glyph = null;
            if ($c2 == 'X') {
                $glyph = (ord($c1) - ord('0')) + 17;
            } else {
                $glyph = ((10 * (ord($c1) - ord('0'))) + (ord($c2) - ord('0'))) + 27;
            }
            $count += $glyph;
            $dest .= $this->telepen_lookup_table[$glyph];
        }

        $check_digit = 127 - ($count % 127);
        if ($check_digit == 127) {
            $check_digit = 0;
        }

        $dest .= $this->telepen_lookup_table[$check_digit];
        $dest .= $this->telepen_lookup_table[ord(self::TELEPEN_STOP_CHAR)]; 

        return $dest;
    }

    
    private function createTelepenConversionTable()
    {
        $this->telepen_lookup_table = [
            "1111111111111111", "1131313111", "33313111", "1111313131", 
            "3111313111", "11333131", "13133131", "111111313111",
            "31333111", "1131113131", "33113131", "1111333111", 
            "3111113131", "1113133111", "1311133111", "111111113131", 
            "3131113111", "11313331", "333331", "111131113111", 
            "31113331", "1133113111", "1313113111", "1111113331", 
            "31131331", "113111113111", "3311113111", "1111131331", 
            "311111113111", "1113111331", "1311111331", "11111111113111", 
            "31313311", "1131311131", "33311131", "1111313311", 
            "3111311131", "11333311", "13133311", "111111311131", 
            "31331131", "1131113311", "33113311", "1111331131", 
            "3111113311", "1113131131", "1311131131", "111111113311", 
            "3131111131", "1131131311", "33131311", "111131111131", 
            "3111131311", "1133111131", "1313111131", "111111131311", 
            "3113111311", "113111111131", "3311111131", "111113111311", 
            "311111111131", "111311111311", "131111111311", "11111111111131", 
            "3131311111", "11313133", "333133", "111131311111", 
            "31113133", "1133311111", "1313311111", "1111113133", 
            "313333", "113111311111", "3311311111", "11113333", 
            "311111311111", "11131333", "13111333", "11111111311111", 
            "31311133", "1131331111", "33331111", "1111311133", 
            "3111331111", "11331133", "13131133", "111111331111", 
            "3113131111", "1131111133", "33111133", "111113131111", 
            "3111111133", "111311131111", "131111131111", "111111111133", 
            "31311313", "113131111111", "3331111111", "1111311313", 
            "311131111111", "11331313", "13131313", "11111131111111", 
            "3133111111", "1131111313", "33111313", "111133111111", 
            "3111111313", "111313111111", "131113111111", "111111111313", 
            "313111111111", "1131131113", "33131113", "11113111111111", 
            "3111131113", "113311111111", "131311111111", "111111131113", 
            "3113111113", "11311111111111", "331111111111", "111113111113", 
            "31111111111111", "111311111113", "131111111113"
        ];
    }
}
