<?php

namespace Identicon\Generator;

use Exception;
use ImagickDraw;
use ImagickPixel;


class ImageMagickGenerator extends BaseGenerator implements GeneratorInterface
{
    
    public function __construct()
    {
        if (!extension_loaded('imagick')) {
            throw new Exception('ImageMagick does not appear to be avaliable in your PHP installation. Please try another generator');
        }
    }

    
    public function getMimeType()
    {
        return 'image/png';
    }

    
    private function generateImage()
    {
        $this->generatedImage = new \Imagick();
        $rgbBackgroundColor = $this->getBackgroundColor();

        if (null === $rgbBackgroundColor) {
            $background = 'none';
        } else {
            $background = new ImagickPixel("rgb($rgbBackgroundColor[0],$rgbBackgroundColor[1],$rgbBackgroundColor[2])");
        }

        $this->generatedImage->newImage($this->pixelRatio * 5, $this->pixelRatio * 5, $background, 'png');

        
        $rgbColor = $this->getColor();
        $color = new ImagickPixel("rgb($rgbColor[0],$rgbColor[1],$rgbColor[2])");

        $draw = new ImagickDraw();
        $draw->setFillColor($color);

        
        foreach ($this->getArrayOfSquare() as $lineKey => $lineValue) {
            foreach ($lineValue as $colKey => $colValue) {
                if (true === $colValue) {
                    $draw->rectangle($colKey * $this->pixelRatio, $lineKey * $this->pixelRatio, ($colKey + 1) * $this->pixelRatio, ($lineKey + 1) * $this->pixelRatio);
                }
            }
        }

        $this->generatedImage->drawImage($draw);

        return $this;
    }

    
    public function getImageBinaryData($string, $size = null, $color = null, $backgroundColor = null)
    {
        ob_start();
        echo $this->getImageResource($string, $size, $color, $backgroundColor);
        $imageData = ob_get_contents();
        ob_end_clean();

        return $imageData;
    }

    
    public function getImageResource($string, $size = null, $color = null, $backgroundColor = null)
    {
        $this
            ->setString($string)
            ->setSize($size)
            ->setColor($color)
            ->setBackgroundColor($backgroundColor)
            ->generateImage();

        return $this->generatedImage;
    }
}
