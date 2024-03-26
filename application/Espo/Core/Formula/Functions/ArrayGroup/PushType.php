<?php


namespace Espo\Core\Formula\Functions\ArrayGroup;

use Espo\Core\Formula\{
    Functions\BaseFunction,
    ArgumentList,
};

class PushType extends BaseFunction
{
    public function process(ArgumentList $args)
    {
        if (count($args) < 2) {
            $this->throwTooFewArguments();
        }

        $list = $this->evaluate($args[0]) ?? [];

        if (!is_array($list)) {
            $this->throwError("Argument is non-array.");
        }

        foreach ($args as $i => $v) {
            if ($i === 0) {
                continue;
            }

            $element = $this->evaluate($args[$i]);

            $list[] = $element;
        }

        return $list;
    }
}
