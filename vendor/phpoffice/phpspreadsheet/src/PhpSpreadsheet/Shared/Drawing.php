<?php

namespace PhpOffice\PhpSpreadsheet\Shared;

use GdImage;

class Drawing
{
    
    public static function pixelsToEMU($pValue)
    {
        return round($pValue * 9525);
    }

    
    public static function EMUToPixels($pValue)
    {
        if ($pValue != 0) {
            return round($pValue / 9525);
        }

        return 0;
    }

    
    public static function pixelsToCellDimension($pValue, \PhpOffice\PhpSpreadsheet\Style\Font $pDefaultFont)
    {
        
        $name = $pDefaultFont->getName();
        $size = $pDefaultFont->getSize();

        if (isset(Font::$defaultColumnWidths[$name][$size])) {
            
            $colWidth = $pValue * Font::$defaultColumnWidths[$name][$size]['width'] / Font::$defaultColumnWidths[$name][$size]['px'];
        } else {
            
            
            $colWidth = $pValue * 11 * Font::$defaultColumnWidths['Calibri'][11]['width'] / Font::$defaultColumnWidths['Calibri'][11]['px'] / $size;
        }

        return $colWidth;
    }

    
    public static function cellDimensionToPixels($pValue, \PhpOffice\PhpSpreadsheet\Style\Font $pDefaultFont)
    {
        
        $name = $pDefaultFont->getName();
        $size = $pDefaultFont->getSize();

        if (isset(Font::$defaultColumnWidths[$name][$size])) {
            
            $colWidth = $pValue * Font::$defaultColumnWidths[$name][$size]['px'] / Font::$defaultColumnWidths[$name][$size]['width'];
        } else {
            
            
            $colWidth = $pValue * $size * Font::$defaultColumnWidths['Calibri'][11]['px'] / Font::$defaultColumnWidths['Calibri'][11]['width'] / 11;
        }

        
        $colWidth = (int) round($colWidth);

        return $colWidth;
    }

    
    public static function pixelsToPoints($pValue)
    {
        return $pValue * 0.75;
    }

    
    public static function pointsToPixels($pValue)
    {
        if ($pValue != 0) {
            return (int) ceil($pValue / 0.75);
        }

        return 0;
    }

    
    public static function degreesToAngle($pValue)
    {
        return (int) round($pValue * 60000);
    }

    
    public static function angleToDegrees($pValue)
    {
        if ($pValue != 0) {
            return round($pValue / 60000);
        }

        return 0;
    }

    
    public static function imagecreatefrombmp($p_sFile)
    {
        
        $file = fopen($p_sFile, 'rb');
        $read = fread($file, 10);
        while (!feof($file) && ($read != '')) {
            $read .= fread($file, 1024);
        }

        $temp = unpack('H*', $read);
        $hex = $temp[1];
        $header = substr($hex, 0, 108);

        
        
        if (substr($header, 0, 4) == '424d') {
            
            $header_parts = str_split($header, 2);

            
            $width = hexdec($header_parts[19] . $header_parts[18]);

            
            $height = hexdec($header_parts[23] . $header_parts[22]);

            
            unset($header_parts);
        }

        
        $x = 0;
        $y = 1;

        
        $image = imagecreatetruecolor($width, $height);

        
        $body = substr($hex, 108);

        
        
        
        $body_size = (strlen($body) / 2);
        $header_size = ($width * $height);

        
        $usePadding = ($body_size > ($header_size * 3) + 4);

        
        
        for ($i = 0; $i < $body_size; $i += 3) {
            
            if ($x >= $width) {
                
                
                if ($usePadding) {
                    $i += $width % 4;
                }

                
                $x = 0;

                
                ++$y;

                
                if ($y > $height) {
                    break;
                }
            }

            
            
            $i_pos = $i * 2;
            $r = hexdec($body[$i_pos + 4] . $body[$i_pos + 5]);
            $g = hexdec($body[$i_pos + 2] . $body[$i_pos + 3]);
            $b = hexdec($body[$i_pos] . $body[$i_pos + 1]);

            
            $color = imagecolorallocate($image, $r, $g, $b);
            imagesetpixel($image, $x, $height - $y, $color);

            
            ++$x;
        }

        
        unset($body);

        
        return $image;
    }
}
