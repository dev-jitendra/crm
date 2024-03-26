<?php

declare(strict_types=1);

namespace Cron;

use DateTime;
use DateTimeInterface;


class DayOfMonthField extends AbstractField
{
    
    protected $rangeStart = 1;

    
    protected $rangeEnd = 31;

    
    private static function getNearestWeekday(int $currentYear, int $currentMonth, int $targetDay): ?DateTime
    {
        $tday = str_pad((string) $targetDay, 2, '0', STR_PAD_LEFT);
        $target = DateTime::createFromFormat('Y-m-d', "${currentYear}-${currentMonth}-${tday}");
        $currentWeekday = (int) $target->format('N');

        if ($currentWeekday < 6) {
            return $target;
        }

        $lastDayOfMonth = $target->format('t');
        foreach ([-1, 1, -2, 2] as $i) {
            $adjusted = $targetDay + $i;
            if ($adjusted > 0 && $adjusted <= $lastDayOfMonth) {
                $target->setDate($currentYear, $currentMonth, $adjusted);

                if ((int) $target->format('N') < 6 && (int) $target->format('m') === $currentMonth) {
                    return $target;
                }
            }
        }
    }

    
    public function isSatisfiedBy(DateTimeInterface $date, $value): bool
    {
        
        if ('?' === $value) {
            return true;
        }

        $fieldValue = $date->format('d');

        
        if ('L' === $value) {
            return $fieldValue === $date->format('t');
        }

        
        if (strpos($value, 'W')) {
            
            $targetDay = (int) substr($value, 0, strpos($value, 'W'));
            
            return $date->format('j') === self::getNearestWeekday(
                    (int) $date->format('Y'),
                    (int) $date->format('m'),
                $targetDay
            )->format('j');
        }

        return $this->isSatisfied((int) $date->format('d'), $value);
    }

    
    public function increment(DateTimeInterface &$date, $invert = false): FieldInterface
    {
        if ($invert) {
            $date = $date->modify('previous day')->setTime(23, 59);
        } else {
            $date = $date->modify('next day')->setTime(0, 0);
        }

        return $this;
    }

    
    public function validate(string $value): bool
    {
        $basicChecks = parent::validate($value);

        
        if (false !== strpos($value, ',') && (false !== strpos($value, 'W') || false !== strpos($value, 'L'))) {
            return false;
        }

        if (!$basicChecks) {
            if ('?' === $value) {
                return true;
            }

            if ('L' === $value) {
                return true;
            }

            if (preg_match('/^(.*)W$/', $value, $matches)) {
                return $this->validate($matches[1]);
            }

            return false;
        }

        return $basicChecks;
    }
}
