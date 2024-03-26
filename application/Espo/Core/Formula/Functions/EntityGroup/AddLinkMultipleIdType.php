<?php


namespace Espo\Core\Formula\Functions\EntityGroup;

use Espo\Core\Exceptions\Error;

class AddLinkMultipleIdType extends \Espo\Core\Formula\Functions\Base
{
    
    public function process(\stdClass $item)
    {
        if (count($item->value) < 2) {
            throw new Error("addLinkMultipleId function: Too few arguments.");
        }

        $link = $this->evaluate($item->value[0]);
        $id = $this->evaluate($item->value[1]);

        if (!is_string($link)) {
            throw new Error();
        }

        if (is_array($id)) {
            $idList = $id;

            foreach ($idList as $id) {
                if (!is_string($id)) {
                    throw new Error();
                }

                $this->getEntity()->addLinkMultipleId($link, $id);
            }
        } else {
            if (!is_string($id)) {
                return;
            }

            $this->getEntity()->addLinkMultipleId($link, $id);
        }
    }
}
