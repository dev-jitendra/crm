<?php


namespace Espo\Core\Formula\Functions\EntityGroup;

use Espo\Core\Exceptions\Error;

class IsAttributeChangedType extends \Espo\Core\Formula\Functions\Base
{
    
    public function process(\stdClass $item)
    {
        if (count($item->value) < 1) {
            throw new Error("isAttributeChanged: too few arguments.");
        }

        $attribute = $this->evaluate($item->value[0]);

        return $this->check($attribute);
    }

    
    protected function check($attribute)
    {
        return $this->getEntity()->isAttributeChanged($attribute);
    }
}
