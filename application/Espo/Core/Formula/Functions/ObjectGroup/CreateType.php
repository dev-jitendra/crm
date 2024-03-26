<?php


namespace Espo\Core\Formula\Functions\ObjectGroup;

use Espo\Core\Formula\ArgumentList;
use Espo\Core\Formula\Functions\BaseFunction;

class CreateType extends BaseFunction
{
    public function process(ArgumentList $args)
    {
        return (object) [];
    }
}
