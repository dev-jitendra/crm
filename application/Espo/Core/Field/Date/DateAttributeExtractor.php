<?php


namespace Espo\Core\Field\Date;

use Espo\ORM\Value\AttributeExtractor;

use Espo\Core\Field\Date;

use stdClass;
use InvalidArgumentException;


class DateAttributeExtractor implements AttributeExtractor
{
    
    public function extract(object $value, string $field): stdClass
    {
        if (!$value instanceof Date) {
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
