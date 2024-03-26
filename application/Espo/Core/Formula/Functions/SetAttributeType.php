<?php


namespace Espo\Core\Formula\Functions;

use Espo\Core\Exceptions\Error;

class SetAttributeType extends Base
{
    
    public function process(\stdClass $item)
    {
        if (count($item->value) < 2) {
            throw new Error("SetAttribute: Too few arguments.");
        }

        $name = $this->evaluate($item->value[0]);

        if (!is_string($name)) {
            throw new Error("SetAttribute: First argument is not string.");
        }

        if ($name === 'id') {
            throw new Error("Formula set-attribute: Not allowed to set `id` attribute.");
        }

        $value = $this->evaluate($item->value[1]);

        $this->getEntity()->set($name, $value);

        return $value;
    }
}
