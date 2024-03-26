<?php


namespace Espo\Core\Formula\Functions;

use Espo\Core\Formula\ArgumentList;
use Espo\Core\Formula\Exceptions\BreakLoop;

class BreakType extends BaseFunction
{
    public function process(ArgumentList $args)
    {
        throw new BreakLoop();
    }
}
