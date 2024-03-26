<?php


namespace Espo\Core\ORM;

use Espo\Core\Utils\Config;
use Espo\ORM\Entity;

class Helper
{
    private const FORMAT_LAST_FIRST = 'lastFirst';
    private const FORMAT_LAST_FIRST_MIDDLE = 'lastFirstMiddle';
    private const FORMAT_FIRST_MIDDLE_LAST = 'firstMiddleLast';

    public function __construct(private Config $config)
    {}

    public function formatPersonName(Entity $entity, string $field): ?string
    {
        $format = $this->config->get('personNameFormat');

        $first = $entity->get('first' . ucfirst($field));
        $last = $entity->get('last' . ucfirst($field));
        $middle = $entity->get('middle' . ucfirst($field));

        switch ($format) {
            case self::FORMAT_LAST_FIRST:
                if (!$first && !$last) {
                    return null;
                }

                if (!$first) {
                    return $last;
                }

                if (!$last) {
                    return $first;
                }

                return $last . ' ' . $first;

            case self::FORMAT_LAST_FIRST_MIDDLE:
                if (!$first && !$last && !$middle) {
                    return null;
                }

                $arr = [];

                if ($last) {
                    $arr[] = $last;
                }

                if ($first) {
                    $arr[] = $first;
                }

                if ($middle) {
                    $arr[] = $middle;
                }

                return implode(' ', $arr);

            case self::FORMAT_FIRST_MIDDLE_LAST:
                if (!$first && !$last && !$middle) {
                    return null;
                }

                $arr = [];

                if ($first) {
                    $arr[] = $first;
                }

                if ($middle) {
                    $arr[] = $middle;
                }

                if ($last) {
                    $arr[] = $last;
                }

                return implode(' ', $arr);
        }

        if (!$first && !$last) {
            return null;
        }

        if (!$first) {
            return $last;
        }

        if (!$last) {
            return $first;
        }

        return $first . ' ' . $last;
    }
}
