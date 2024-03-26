<?php


namespace Espo\Core\Formula\Functions\EntityGroup;

use Espo\Core\Exceptions\Error;

class AttributeType extends \Espo\Core\Formula\Functions\AttributeType
{
    public function process(\stdClass $item)
    {
        if (count($item->value) < 1) {
            throw new Error("attribute function: Too few arguments.");
        }

        $attribute = $this->evaluate($item->value[0]);

        return $this->getAttributeValue($attribute);
    }
}
