<?php

namespace Picqer\Barcode;

class BarcodeGeneratorDynamicHTML extends BarcodeGenerator
{
    private const WIDTH_PRECISION = 6;

    
    public function getBarcode(string $barcode, $type, string $foregroundColor = 'black'): string
    {
        $barcodeData = $this->getBarcodeData($barcode, $type);

        $html = '<div style="font-size:0;position:relative;width:100%;height:100%">' . PHP_EOL;

        $positionHorizontal = 0;
        
        foreach ($barcodeData->getBars() as $bar) {
            $barWidth = $bar->getWidth() / $barcodeData->getWidth() * 100;
            $barHeight = round(($bar->getHeight() / $barcodeData->getHeight() * 100), 3);

            if ($bar->isBar() && $barWidth > 0) {
                $positionVertical = round(($bar->getPositionVertical() / $barcodeData->getHeight() * 100), 3);

                
                $html .= '<div style="background-color:' . $foregroundColor . ';width:' . round($barWidth, self::WIDTH_PRECISION) . '%;height:' . $barHeight . '%;position:absolute;left:' . round($positionHorizontal, self::WIDTH_PRECISION) . '%;top:' . $positionVertical . (($positionVertical > 0) ? '%' : '') . '">&nbsp;</div>' . PHP_EOL;
            }

            $positionHorizontal += $barWidth;
        }

        $html .= '</div>' . PHP_EOL;

        return $html;
    }
}
