<?php

declare(strict_types=1);

namespace OpenSpout\Writer\ODS\Helper;

use OpenSpout\Common\Entity\Style\Border;
use OpenSpout\Common\Entity\Style\BorderPart;


final class BorderHelper
{
    
    public const widthMap = [
        Border::WIDTH_THIN => '0.75pt',
        Border::WIDTH_MEDIUM => '1.75pt',
        Border::WIDTH_THICK => '2.5pt',
    ];

    
    public const styleMap = [
        Border::STYLE_SOLID => 'solid',
        Border::STYLE_DASHED => 'dashed',
        Border::STYLE_DOTTED => 'dotted',
        Border::STYLE_DOUBLE => 'double',
    ];

    public static function serializeBorderPart(BorderPart $borderPart): string
    {
        $definition = 'fo:border-%s="%s"';

        if (Border::STYLE_NONE === $borderPart->getStyle()) {
            $borderPartDefinition = sprintf($definition, $borderPart->getName(), 'none');
        } else {
            $attributes = [
                self::widthMap[$borderPart->getWidth()],
                self::styleMap[$borderPart->getStyle()],
                '#'.$borderPart->getColor(),
            ];
            $borderPartDefinition = sprintf($definition, $borderPart->getName(), implode(' ', $attributes));
        }

        return $borderPartDefinition;
    }
}
