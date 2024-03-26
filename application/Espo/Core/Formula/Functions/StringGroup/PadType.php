<?php


namespace Espo\Core\Formula\Functions\StringGroup;

use Espo\Core\Formula\{
    Functions\BaseFunction,
    ArgumentList,
};

class PadType extends BaseFunction
{
    public function process(ArgumentList $args)
    {
        $args = $this->evaluate($args);

        if (count($args) < 2) {
            $this->throwTooFewArguments();
        }

        $input = $args[0];
        $length = $args[1];
        $string = $args[2] ?? ' ';
        $type = $args[3] ?? 'right';

        if (!is_string($input)) {
            $input = strval($input);
        }

        if (!is_int($length)) {
            $this->throwBadArgumentType(2);
        }

        $map = [
            'right' => \STR_PAD_RIGHT,
            'left' => \STR_PAD_LEFT,
            'both' => \STR_PAD_BOTH,
        ];

        $padType = $map[$type] ?? \STR_PAD_RIGHT;

        return str_pad($input, $length, $string, $padType);
    }
}
