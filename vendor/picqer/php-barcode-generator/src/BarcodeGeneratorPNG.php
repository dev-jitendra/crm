<?php

namespace Picqer\Barcode;

use Imagick;
use imagickdraw;
use imagickpixel;
use Picqer\Barcode\Exceptions\BarcodeException;

class BarcodeGeneratorPNG extends BarcodeGenerator
{
    protected $useImagick = true;

    
    public function __construct()
    {
        
        if (extension_loaded('imagick')) {
            $this->useImagick = true;
        } elseif (function_exists('imagecreate')) {
            $this->useImagick = false;
        } else {
            throw new BarcodeException('Neither gd-lib or imagick are installed!');
        }
    }

    
    public function useImagick()
    {
        $this->useImagick = true;
    }

    
    public function useGd()
    {
        $this->useImagick = false;
    }

    
    public function getBarcode(string $barcode, $type, int $widthFactor = 2, int $height = 30, array $foregroundColor = [0, 0, 0]): string
    {
        $barcodeData = $this->getBarcodeData($barcode, $type);
        $width = round($barcodeData->getWidth() * $widthFactor);

        if ($this->useImagick) {
            $imagickBarsShape = new imagickdraw();
            $imagickBarsShape->setFillColor(new imagickpixel('rgb(' . implode(',', $foregroundColor) .')'));
        } else {
            $image = $this->createGdImageObject($width, $height);
            $gdForegroundColor = imagecolorallocate($image, $foregroundColor[0], $foregroundColor[1], $foregroundColor[2]);
        }

        
        $positionHorizontal = 0;
        
        foreach ($barcodeData->getBars() as $bar) {
            $barWidth = round(($bar->getWidth() * $widthFactor), 3);

            if ($bar->isBar() && $barWidth > 0) {
                $y = round(($bar->getPositionVertical() * $height / $barcodeData->getHeight()), 3);
                $barHeight = round(($bar->getHeight() * $height / $barcodeData->getHeight()), 3);

                
                if ($this->useImagick) {
                    $imagickBarsShape->rectangle($positionHorizontal, $y, ($positionHorizontal + $barWidth - 1), ($y + $barHeight));
                } else {
                    imagefilledrectangle($image, $positionHorizontal, $y, ($positionHorizontal + $barWidth - 1), ($y + $barHeight), $gdForegroundColor);
                }
            }
            $positionHorizontal += $barWidth;
        }

        if ($this->useImagick) {
            $image = $this->createImagickImageObject($width, $height);
            $image->drawImage($imagickBarsShape);
            return $image->getImageBlob();
        }

        ob_start();
        $this->generateGdImage($image);
        return ob_get_clean();
    }

    protected function createGdImageObject(int $width, int $height)
    {
        $image = imagecreate($width, $height);
        $colorBackground = imagecolorallocate($image, 255, 255, 255);
        imagecolortransparent($image, $colorBackground);

        return $image;
    }

    protected function createImagickImageObject(int $width, int $height): Imagick
    {
        $image = new Imagick();
        $image->newImage($width, $height, 'none', 'PNG');

        return $image;
    }

    protected function generateGdImage($image)
    {
        imagepng($image);
        imagedestroy($image);
    }
}
