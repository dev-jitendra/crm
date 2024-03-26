<?php

declare(strict_types=1);

namespace OpenSpout\Writer\Common\Manager\Style;

use OpenSpout\Common\Entity\Cell;
use OpenSpout\Common\Entity\Style\Style;


interface StyleManagerInterface
{
    
    public function registerStyle(Style $style): Style;

    
    public function applyExtraStylesIfNeeded(Cell $cell): PossiblyUpdatedStyle;
}
