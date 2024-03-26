<?php


namespace Espo\Core\Formula\Functions;

use Espo\Core\Formula\{
    Functions\BaseFunction,
    ArgumentList,
};

class BundleType extends BaseFunction
{
    public function process(ArgumentList $args)
    {
        foreach ($args as $item) {
            $this->evaluate($item);
        }
    }
}
