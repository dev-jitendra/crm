<?php


namespace Espo\Core\Formula\Functions\EntityGroup;

class AttributeFetchedType extends AttributeType
{
    protected function getAttributeValue($attribute)
    {
        return $this->attributeFetcher->fetch($this->getEntity(), $attribute, true);
    }
}
