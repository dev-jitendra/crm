<?php


namespace Espo\Core\Formula\Functions;

use Espo\Core\Formula\ArgumentList;
use Espo\Core\Formula\Exceptions\ContinueLoop;

class ContinueType extends BaseFunction
{
    public function process(ArgumentList $args)
    {
        throw new ContinueLoop();
    }
}
