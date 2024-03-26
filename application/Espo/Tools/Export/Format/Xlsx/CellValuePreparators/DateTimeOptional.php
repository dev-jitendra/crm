<?php


namespace Espo\Tools\Export\Format\Xlsx\CellValuePreparators;

use Espo\Core\Field\DateTime as DateTimeValue;
use Espo\Core\Field\Date as DateValue;
use Espo\Core\Utils\Config;
use Espo\ORM\Entity;
use Espo\Tools\Export\Format\CellValuePreparator;

use DateTimeZone;

class DateTimeOptional implements CellValuePreparator
{
    private string $timezone;

    public function __construct(Config $config)
    {
        $this->timezone = $config->get('timeZone') ?? 'UTC';
    }

    public function prepare(Entity $entity, string $name): DateTimeValue|DateValue|null
    {
        $dateValue = $entity->get($name . 'Date');

        if ($dateValue !== null) {
            return DateValue::fromString($dateValue);
        }

        $value = $entity->get($name);

        if (!$value) {
            return null;
        }

        return DateTimeValue::fromString($value)
            ->withTimezone(
                new DateTimeZone($this->timezone)
            );
    }
}
