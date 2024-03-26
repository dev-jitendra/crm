<?php


namespace Espo\Tools\Export\Format\Xlsx\CellValuePreparators;

use Espo\Core\Field\Currency as CurrencyValue;
use Espo\Core\Utils\Config;
use Espo\ORM\Entity;
use Espo\Tools\Export\Format\CellValuePreparator;

class CurrencyConverted implements CellValuePreparator
{
    private string $code;

    public function __construct(Config $config)
    {
        $this->code = $config->get('defaultCurrency');
    }

    public function prepare(Entity $entity, string $name): ?CurrencyValue
    {
        $value = $entity->get($name);

        if ($value === null) {
            return null;
        }

        return CurrencyValue::create($value, $this->code);
    }
}
