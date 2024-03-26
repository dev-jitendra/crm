<?php


namespace Espo\Core\Formula\Functions\ArrayGroup;

use Espo\Core\Formula\{
    Functions\BaseFunction,
    ArgumentList,
};

class JoinType extends BaseFunction
{
    public function process(ArgumentList $args)
    {
        if (count($args) < 2) {
            $this->throwTooFewArguments();
        }

        $list = $this->evaluate($args[0]) ?? [];

        $separator = $this->evaluate($args[1]);

        if (!is_string($separator)) {
            $this->throwBadArgumentValue(2, 'string');
        }

        if (is_null($list)) {
            return '';
        }

        return implode($separator, $list);
    }
}
