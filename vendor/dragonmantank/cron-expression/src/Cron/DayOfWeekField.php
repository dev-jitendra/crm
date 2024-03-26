<?php

declare(strict_types=1);

namespace Cron;

use DateTime;
use DateTimeInterface;
use InvalidArgumentException;


class DayOfWeekField extends AbstractField
{
    
    protected $rangeStart = 0;

    
    protected $rangeEnd = 7;

    
    protected $nthRange;

    
    protected $literals = [1 => 'MON', 2 => 'TUE', 3 => 'WED', 4 => 'THU', 5 => 'FRI', 6 => 'SAT', 7 => 'SUN'];

    
    public function __construct()
    {
        $this->nthRange = range(1, 5);
        parent::__construct();
    }

    
    public function isSatisfiedBy(DateTimeInterface $date, $value): bool
    {
        if ('?' === $value) {
            return true;
        }

        
        $value = $this->convertLiterals($value);

        $currentYear = (int) $date->format('Y');
        $currentMonth = (int) $date->format('m');
        $lastDayOfMonth = (int) $date->format('t');

        
        if (strpos($value, 'L')) {
            $weekday = (int) $this->convertLiterals(substr($value, 0, strpos($value, 'L')));
            $weekday %= 7;

            $tdate = clone $date;
            $tdate = $tdate->setDate($currentYear, $currentMonth, $lastDayOfMonth);
            while ($tdate->format('w') != $weekday) {
                $tdateClone = new DateTime();
                $tdate = $tdateClone
                    ->setTimezone($tdate->getTimezone())
                    ->setDate($currentYear, $currentMonth, --$lastDayOfMonth);
            }

            return (int) $date->format('j') === $lastDayOfMonth;
        }

        
        if (strpos($value, '#')) {
            [$weekday, $nth] = explode('#', $value);

            if (!is_numeric($nth)) {
                throw new InvalidArgumentException("Hashed weekdays must be numeric, {$nth} given");
            } else {
                $nth = (int) $nth;
            }

            
            if ('0' === $weekday) {
                $weekday = 7;
            }

            $weekday = (int) $this->convertLiterals((string) $weekday);

            
            if ($weekday < 0 || $weekday > 7) {
                throw new InvalidArgumentException("Weekday must be a value between 0 and 7. {$weekday} given");
            }

            if (!\in_array($nth, $this->nthRange, true)) {
                throw new InvalidArgumentException("There are never more than 5 or less than 1 of a given weekday in a month, {$nth} given");
            }

            
            if ((int) $date->format('N') !== $weekday) {
                return false;
            }

            $tdate = clone $date;
            $tdate = $tdate->setDate($currentYear, $currentMonth, 1);
            $dayCount = 0;
            $currentDay = 1;
            while ($currentDay < $lastDayOfMonth + 1) {
                if ((int) $tdate->format('N') === $weekday) {
                    if (++$dayCount >= $nth) {
                        break;
                    }
                }
                $tdate = $tdate->setDate($currentYear, $currentMonth, ++$currentDay);
            }

            return (int) $date->format('j') === $currentDay;
        }

        
        if (false !== strpos($value, '-')) {
            $parts = explode('-', $value);
            if ('7' === $parts[0]) {
                $parts[0] = 0;
            } elseif ('0' === $parts[1]) {
                $parts[1] = 7;
            }
            $value = implode('-', $parts);
        }

        
        $format = \in_array(7, array_map(function ($value) {
            return (int) $value;
        }, str_split($value)), true) ? 'N' : 'w';
        $fieldValue = (int) $date->format($format);

        return $this->isSatisfied($fieldValue, $value);
    }

    
    public function increment(DateTimeInterface &$date, $invert = false): FieldInterface
    {
        if ($invert) {
            $date = $date->modify('-1 day')->setTime(23, 59, 0);
        } else {
            $date = $date->modify('+1 day')->setTime(0, 0, 0);
        }

        return $this;
    }

    
    public function validate(string $value): bool
    {
        $basicChecks = parent::validate($value);

        if (!$basicChecks) {
            if ('?' === $value) {
                return true;
            }

            
            if (false !== strpos($value, '#')) {
                $chunks = explode('#', $value);
                $chunks[0] = $this->convertLiterals($chunks[0]);

                if (parent::validate($chunks[0]) && is_numeric($chunks[1]) && \in_array((int) $chunks[1], $this->nthRange, true)) {
                    return true;
                }
            }

            if (preg_match('/^(.*)L$/', $value, $matches)) {
                return $this->validate($matches[1]);
            }

            return false;
        }

        return $basicChecks;
    }
}
