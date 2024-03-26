<?php


namespace Espo\Core\Field\Link;

use Espo\ORM\Value\AttributeExtractor;

use Espo\Core\Field\Link;

use stdClass;
use InvalidArgumentException;


class LinkAttributeExtractor implements AttributeExtractor
{
    
    public function extract(object $value, string $field): stdClass
    {
        if (!$value instanceof Link) {
            throw new InvalidArgumentException();
        }

        return (object) [
            $field . 'Id' => $value->getId(),
            $field . 'Name' => $value->getName(),
        ];
    }

    public function extractFromNull(string $field): stdClass
    {
        return (object) [
            $field . 'Id' => null,
            $field . 'Name' => null,
        ];
    }
}
