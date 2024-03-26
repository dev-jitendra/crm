<?php


namespace Espo\Core\Field\DateTimeOptional;

use Espo\ORM\Value\AttributeExtractor;

use Espo\Core\Field\DateTimeOptional;

use stdClass;
use InvalidArgumentException;


class DateTimeOptionalAttributeExtractor implements AttributeExtractor
{
    
    public function extract(object $value, string $field): stdClass
    {
        if (!$value instanceof DateTimeOptional) {
            throw new InvalidArgumentException();
        }

        if ($value->isAllDay()) {
            return (object) [
                $field . 'Date' => $value->toString(),
                $field => null,
            ];
        }

        return (object) [
            $field => $value->toString(),
            $field . 'Date' => null,
        ];
    }

    public function extractFromNull(string $field): stdClass
    {
        return (object) [
            $field => null,
            $field . 'Date' => null,
        ];
    }
}
