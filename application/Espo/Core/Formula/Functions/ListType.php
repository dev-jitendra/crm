<?php


namespace Espo\Core\Formula\Functions;

use Espo\Core\Formula\{
    Functions\BaseFunction,
    ArgumentList,
};

class ListType extends BaseFunction
{
    public function process(ArgumentList $args)
    {
        $result = [];

        foreach ($args as $item) {
            $result[] = $this->evaluate($item);
        }

        return $result;
    }
}
