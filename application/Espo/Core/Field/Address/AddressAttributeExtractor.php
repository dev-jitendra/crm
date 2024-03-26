<?php


namespace Espo\Core\Field\Address;

use Espo\ORM\Value\AttributeExtractor;

use Espo\Core\Field\Address;

use stdClass;
use InvalidArgumentException;


class AddressAttributeExtractor implements AttributeExtractor
{
    public function extract(object $value, string $field): stdClass
    {
        if (!$value instanceof Address) {
            throw new InvalidArgumentException();
        }

        return (object) [
            $field . 'Street' => $value->getStreet(),
            $field . 'City' => $value->getCity(),
            $field . 'Country' => $value->getCountry(),
            $field . 'State' => $value->getState(),
            $field . 'PostalCode' => $value->getPostalCode(),
        ];
    }

    public function extractFromNull(string $field): stdClass
    {
        return (object) [
            $field . 'Street' => null,
            $field . 'City' => null,
            $field . 'Country' => null,
            $field . 'State' => null,
            $field . 'PostalCode' => null,
        ];
    }
}
