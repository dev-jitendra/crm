<?php

namespace Picqer\Barcode;

class BarcodeGeneratorSVG extends BarcodeGenerator
{
    
    public function getBarcode(string $barcode, $type, float $widthFactor = 2, float $height = 30, string $foregroundColor = 'black'): string
    {
        $barcodeData = $this->getBarcodeData($barcode, $type);

        
        $repstr = [
            "\0" => '',
            '&' => '&amp;',
            '<' => '&lt;',
            '>' => '&gt;',
        ];

        $width = round(($barcodeData->getWidth() * $widthFactor), 3);

        $svg = '<?xml version="1.0" standalone="no" ?>' . PHP_EOL;
        $svg .= '<!DOCTYPE svg PUBLIC "-
        $svg .= '<svg width="' . $width . '" height="' . $height . '" viewBox="0 0 ' . $width . ' ' . $height . '" version="1.1" xmlns="http:
        $svg .= "\t" . '<desc>' . strtr($barcodeData->getBarcode(), $repstr) . '</desc>' . PHP_EOL;
        $svg .= "\t" . '<g id="bars" fill="' . $foregroundColor . '" stroke="none">' . PHP_EOL;

        
        $positionHorizontal = 0;
        
        foreach ($barcodeData->getBars() as $bar) {
            $barWidth = round(($bar->getWidth() * $widthFactor), 3);
            $barHeight = round(($bar->getHeight() * $height / $barcodeData->getHeight()), 3);

            if ($bar->isBar() && $barWidth > 0) {
                $positionVertical = round(($bar->getPositionVertical() * $height / $barcodeData->getHeight()), 3);
                
                $svg .= "\t\t" . '<rect x="' . $positionHorizontal . '" y="' . $positionVertical . '" width="' . $barWidth . '" height="' . $barHeight . '" />' . PHP_EOL;
            }

            $positionHorizontal += $barWidth;
        }

        $svg .= "\t</g>" . PHP_EOL;
        $svg .= '</svg>' . PHP_EOL;

        return $svg;
    }
}
