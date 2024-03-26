<?php


namespace Espo\Core\Formula\Functions\EntityGroup;

use Espo\Core\Exceptions\Error;

class HasLinkMultipleIdType extends \Espo\Core\Formula\Functions\Base
{
    
    public function process(\stdClass $item)
    {
        if (count($item->value) < 2) {
            throw new Error("hasLinkMultipleId: too few arguments.");
        }

        $link = $this->evaluate($item->value[0]);
        $id = $this->evaluate($item->value[1]);

        if (!is_string($link)) {
            throw new Error();
        }

        if (!is_string($id)) {
            throw new Error();
        }

        return $this->getEntity()->hasLinkMultipleId($link, $id);
    }
}
