<?php

namespace Identicon\Generator;

use Exception;


class BaseGenerator
{
    
    protected $generatedImage;

    
    protected $color;

    
    protected $backgroundColor;

    
    protected $size;

    
    protected $pixelRatio;

    
    private $hash;

    
    private $arrayOfSquare = [];

    
    public function setColor($color)
    {
        if (null === $color) {
            return $this;
        }

        $this->color = $this->convertColor($color);

        return $this;
    }

    
    public function setBackgroundColor($backgroundColor)
    {
        if (null === $backgroundColor) {
            return $this;
        }

        $this->backgroundColor = $this->convertColor($backgroundColor);

        return $this;
    }

    
    private function convertColor($color)
    {
        if (is_array($color)) {
            return $color;
        }

        if (preg_match('/^#?([a-z\d])([a-z\d])([a-z\d])$/i', $color, $matches)) {
            $color = $matches[1].$matches[1];
            $color .= $matches[2].$matches[2];
            $color .= $matches[3].$matches[3];
        }

        preg_match('/#?([a-z\d]{2})([a-z\d]{2})([a-z\d]{2})$/i', $color, $matches);

        return array_map(function ($value) {
            return hexdec($value);
        }, array_slice($matches, 1, 3));
    }

    
    public function getColor()
    {
        return $this->color;
    }

    
    public function getBackgroundColor()
    {
        return $this->backgroundColor;
    }

    
    private function convertHashToArrayOfBoolean()
    {
        preg_match_all('/(\w)(\w)/', $this->hash, $chars);

        foreach ($chars[1] as $i => $char) {
            $index = (int) ($i / 3);
            $data = $this->convertHexaToBoolean($char);

            $items = [
                0 => [0, 4],
                1 => [1, 3],
                2 => [2],
            ];

            foreach ($items[$i % 3] as $item) {
                $this->arrayOfSquare[$index][$item] = $data;
            }

            ksort($this->arrayOfSquare[$index]);
        }

        $this->color = array_map(function ($data) {
            return hexdec($data) * 16;
        }, array_reverse($chars[1]));

        return $this;
    }

    
    private function convertHexaToBoolean($hexa)
    {
        return (bool) round(hexdec($hexa) / 10);
    }

    
    public function getArrayOfSquare()
    {
        return $this->arrayOfSquare;
    }

    
    public function getHash()
    {
        return $this->hash;
    }

    
    public function setString($string)
    {
        if (null === $string) {
            throw new Exception('The string cannot be null.');
        }

        $this->hash = md5($string);

        $this->convertHashToArrayOfBoolean();

        return $this;
    }

    
    public function setSize($size)
    {
        if (null === $size) {
            return $this;
        }

        $this->size = $size;
        $this->pixelRatio = (int) round($size / 5);

        return $this;
    }

    
    public function getSize()
    {
        return $this->size;
    }

    
    public function getPixelRatio()
    {
        return $this->pixelRatio;
    }
}
