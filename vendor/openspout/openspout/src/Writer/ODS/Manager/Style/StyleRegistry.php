<?php

declare(strict_types=1);

namespace OpenSpout\Writer\ODS\Manager\Style;

use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\Common\Manager\Style\AbstractStyleRegistry as CommonStyleRegistry;


final class StyleRegistry extends CommonStyleRegistry
{
    
    private array $usedFontsSet = [];

    
    public function registerStyle(Style $style): Style
    {
        if ($style->isRegistered()) {
            return $style;
        }

        $registeredStyle = parent::registerStyle($style);
        $this->usedFontsSet[$style->getFontName()] = true;

        return $registeredStyle;
    }

    
    public function getUsedFonts(): array
    {
        return array_keys($this->usedFontsSet);
    }
}
