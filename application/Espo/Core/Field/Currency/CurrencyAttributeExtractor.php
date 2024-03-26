<?php


namespace Espo\Core\Field\Currency;

use Espo\ORM\Defs;
use Espo\ORM\Entity;
use Espo\ORM\Value\AttributeExtractor;

use Espo\Core\Field\Currency;

use stdClass;
use InvalidArgumentException;


class CurrencyAttributeExtractor implements AttributeExtractor
{
    public function __construct(
        private string $entityType,
        private Defs $ormDefs
    ) {}

    public function extract(object $value, string $field): stdClass
    {
        if (!$value instanceof Currency) {
            throw new InvalidArgumentException();
        }

        $useString = $this->ormDefs
            ->getEntity($this->entityType)
            ->getField($field)
            ->getType() === Entity::VARCHAR;

        $amount = $useString ?
            $value->getAmountAsString() :
            $value->getAmount();

        return (object) [
            $field => $amount,
            $field . 'Currency' => $value->getCode(),
        ];
    }

    public function extractFromNull(string $field): stdClass
    {
        return (object) [
            $field => null,
            $field . 'Currency' => null,
        ];
    }
}
