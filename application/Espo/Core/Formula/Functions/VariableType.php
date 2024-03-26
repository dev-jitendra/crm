<?php


namespace Espo\Core\Formula\Functions;

use Espo\Core\Formula\Exceptions\Error;
use Espo\Core\Formula\ArgumentList;

class VariableType extends BaseFunction
{
    public function process(ArgumentList $args)
    {
        if (!count($args)) {
            throw new Error("No variable name.");
        }

        $name = $args[0]->getData();

        if (!is_string($name)) {
            throw new Error("Bad variable name.");
        }

        if ($name === '') {
            throw new Error("Empty variable name.");
        }

        if (!property_exists($this->getVariables(), $name)) {
            return null;
        }

        return $this->getVariables()->$name;
    }
}
