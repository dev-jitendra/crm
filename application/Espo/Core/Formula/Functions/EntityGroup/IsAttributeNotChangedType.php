<?php


namespace Espo\Core\Formula\Functions\EntityGroup;

class IsAttributeNotChangedType extends IsAttributeChangedType
{
    
    protected function check($attribute)
    {
        return !parent::check($attribute);
    }
}
