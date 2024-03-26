<?php


namespace Espo\Core\Formula\Functions\StringGroup;

use Espo\Core\Formula\{
    Functions\BaseFunction,
    ArgumentList,
};

class ConcatenationType extends BaseFunction
{
    public function process(ArgumentList $args)
    {
        $result = '';

        foreach ($args as $subItem) {
            $part = $this->evaluate($subItem);

            if (!is_string($part)) {
                $part = strval($part);
            }

            $result .= $part;
        }

        return $result;
    }
}
