<?php


namespace Espo\Core\Formula\Functions\EntityGroup;

class IsNewType extends \Espo\Core\Formula\Functions\Base
{
    
    public function process(\stdClass $item)
    {
        return $this->getEntity()->isNew();
    }
}
