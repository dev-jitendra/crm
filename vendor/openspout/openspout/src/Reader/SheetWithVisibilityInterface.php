<?php

declare(strict_types=1);

namespace OpenSpout\Reader;


interface SheetWithVisibilityInterface extends SheetInterface
{
    
    public function isVisible(): bool;
}
