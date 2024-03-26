<?php

namespace Identicon\Generator;


class SvgGenerator extends BaseGenerator implements GeneratorInterface
{
    
    public function getMimeType()
    {
        return 'image/svg+xml';
    }

    
    public function getImageBinaryData($string, $size = null, $color = null, $backgroundColor = null)
    {
        return $this->getImageResource($string, $size, $color, $backgroundColor);
    }

    
    public function getImageResource($string, $size = null, $color = null, $backgroundColor = null)
    {
        $this
            ->setString($string)
            ->setSize($size)
            ->setColor($color)
            ->setBackgroundColor($backgroundColor)
            ->_generateImage();

        return $this->generatedImage;
    }

    
    protected function _generateImage()
    {
        
        $w = $this->getPixelRatio() * 5;
        $h = $this->getPixelRatio() * 5;
        $svg = '<svg xmlns="http:

        $backgroundColor = '#FFF';
        $rgbBackgroundColor = $this->getBackgroundColor();
        if (!is_null($rgbBackgroundColor)) {
            $backgroundColor = $this->_toUnderstandableColor($rgbBackgroundColor);
        }

        $svg .= '<rect width="5" height="5" fill="'.$backgroundColor.'" stroke-width="0"/>';

        $rects = [];
        
        foreach ($this->getArrayOfSquare() as $lineKey => $lineValue) {
            foreach ($lineValue as $colKey => $colValue) {
                if (true === $colValue) {
                    $rects[] = 'M'.$colKey.','.$lineKey.'h1v1h-1v-1';
                }
            }
        }

        $rgbColor = $this->_toUnderstandableColor($this->getColor());
        $svg .= '<path fill="'.$rgbColor.'" stroke-width="0" d="' . implode('', $rects) . '"/>';
        $svg .= '</svg>';

        $this->generatedImage = $svg;

        return $this;
    }

    
    protected function _toUnderstandableColor($color)
    {
        if (is_array($color)) {
            return sprintf('#%X%X%X', $color[0], $color[1], $color[2]);
        }

        return $color;
    }
}
