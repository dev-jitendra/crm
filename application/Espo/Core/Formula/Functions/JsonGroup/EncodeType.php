<?php


namespace Espo\Core\Formula\Functions\JsonGroup;

use Espo\Core\Formula\Functions\BaseFunction;
use Espo\Core\Formula\ArgumentList;

use Espo\Core\Utils\Json;

class EncodeType extends BaseFunction
{
    public function process(ArgumentList $args)
    {
        if (count($args) < 1) {
            $this->throwTooFewArguments();
        }

        $value = $this->evaluate($args[0]);

        return Json::encode($value);
    }
}
