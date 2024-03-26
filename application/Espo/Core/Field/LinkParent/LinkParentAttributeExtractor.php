<?php


namespace Espo\Core\Field\LinkParent;

use Espo\ORM\Value\AttributeExtractor;

use Espo\Core\Field\LinkParent;

use stdClass;
use InvalidArgumentException;


class LinkParentAttributeExtractor implements AttributeExtractor
{
    
    public function extract(object $value, string $field): stdClass
    {
        if (!$value instanceof LinkParent) {
            throw new InvalidArgumentException();
        }

        return (object) [
            $field . 'Id' => $value->getId(),
            $field . 'Type' => $value->getEntityType(),
            $field . 'Name' => $value->getName(),
        ];
    }

    public function extractFromNull(string $field): stdClass
    {
        return (object) [
            $field . 'Id' => null,
            $field . 'Type' => null,
            $field . 'Name' => null,
        ];
    }
}
