<?php


namespace Espo\Core\Formula\Functions;

use Espo\Core\Exceptions\Error;
use Espo\Core\Formula\AttributeFetcher;

class AttributeType extends Base
{
    
    protected $attributeFetcher;

    
    public function setAttributeFetcher(AttributeFetcher $attributeFetcher)
    {
        $this->attributeFetcher = $attributeFetcher;
    }

    
    public function process(\stdClass $item)
    {
        if (!property_exists($item, 'value')) {
            throw new Error();
        }

        return $this->getAttributeValue($item->value);
    }

    
    protected function getAttributeValue($attribute)
    {
        return $this->attributeFetcher->fetch($this->getEntity(), $attribute);
    }
}
