<?php


namespace Espo\Core\Field\DateTime;

use Espo\ORM\Value\AttributeExtractor;

use Espo\Core\Field\DateTime;

use stdClass;
use InvalidArgumentException;


class DateTimeAttributeExtractor implements AttributeExtractor
{
    
    public function extract(object $value, string $field): stdClass
    {
        if (!$value instanceof DateTime) {
            throw new InvalidArgumentException();
        }

        return (object) [
            $field => $value->toString(),
        ];
    }

    public function extractFromNull(string $field): stdClass
    {
        return (object) [
            $field => null,
        ];
    }
}
