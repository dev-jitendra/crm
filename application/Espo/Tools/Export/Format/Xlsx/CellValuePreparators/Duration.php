<?php


namespace Espo\Tools\Export\Format\Xlsx\CellValuePreparators;

use Espo\Core\Utils\Language;
use Espo\ORM\Entity;
use Espo\Tools\Export\Format\CellValuePreparator;

class Duration implements CellValuePreparator
{
    public function __construct(private Language $language)
    {}

    public function prepare(Entity $entity, string $name): ?string
    {
        $value = $entity->get($name);

        if (!$value) {
            return null;
        }

        $seconds = intval($value);

        $days = intval(floor($seconds / 86400));
        $seconds = $seconds - $days * 86400;
        $hours = intval(floor($seconds / 3600));
        $seconds = $seconds - $hours * 3600;
        $minutes = intval(floor($seconds / 60));

        $value = '';

        if ($days) {
            $value .= $days . $this->language->translateLabel('d', 'durationUnits');

            if ($minutes || $hours) {
                $value .= ' ';
            }
        }

        if ($hours) {
            $value .= $hours . $this->language->translateLabel('h', 'durationUnits');

            if ($minutes) {
                $value .= ' ';
            }
        }

        if ($minutes) {
            $value .= $minutes . $this->language->translateLabel('m', 'durationUnits');
        }

        return $value;
    }
}
