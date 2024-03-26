<?php

declare(strict_types=1);

namespace Cron;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Exception;
use InvalidArgumentException;
use RuntimeException;


class CronExpression
{
    public const MINUTE = 0;
    public const HOUR = 1;
    public const DAY = 2;
    public const MONTH = 3;
    public const WEEKDAY = 4;
    public const YEAR = 5;

    
    private $cronParts;

    
    private $fieldFactory;

    
    private $maxIterationCount = 1000;

    
    private static $order = [self::YEAR, self::MONTH, self::DAY, self::WEEKDAY, self::HOUR, self::MINUTE];

    
    public static function factory(string $expression, FieldFactoryInterface $fieldFactory = null): CronExpression
    {
        $mappings = [
            '@yearly' => '0 0 1 1 *',
            '@annually' => '0 0 1 1 *',
            '@monthly' => '0 0 1 * *',
            '@weekly' => '0 0 * * 0',
            '@daily' => '0 0 * * *',
            '@hourly' => '0 * * * *',
        ];

        $shortcut = strtolower($expression);
        if (isset($mappings[$shortcut])) {
            $expression = $mappings[$shortcut];
        }

        return new static($expression, $fieldFactory ?: new FieldFactory());
    }

    
    public static function isValidExpression(string $expression): bool
    {
        try {
            self::factory($expression);
        } catch (InvalidArgumentException $e) {
            return false;
        }

        return true;
    }

    
    public function __construct(string $expression, FieldFactory $fieldFactory = null)
    {
        $this->fieldFactory = $fieldFactory ?: new FieldFactory();
        $this->setExpression($expression);
    }

    
    public function setExpression(string $value): CronExpression
    {
        $this->cronParts = preg_split('/\s/', $value, -1, PREG_SPLIT_NO_EMPTY);
        if (\count($this->cronParts) < 5) {
            throw new InvalidArgumentException(
                $value . ' is not a valid CRON expression'
            );
        }

        foreach ($this->cronParts as $position => $part) {
            $this->setPart($position, $part);
        }

        return $this;
    }

    
    public function setPart(int $position, string $value): CronExpression
    {
        if (!$this->fieldFactory->getField($position)->validate($value)) {
            throw new InvalidArgumentException(
                'Invalid CRON field value ' . $value . ' at position ' . $position
            );
        }

        $this->cronParts[$position] = $value;

        return $this;
    }

    
    public function setMaxIterationCount(int $maxIterationCount): CronExpression
    {
        $this->maxIterationCount = $maxIterationCount;

        return $this;
    }

    
    public function getNextRunDate($currentTime = 'now', int $nth = 0, bool $allowCurrentDate = false, $timeZone = null): DateTime
    {
        return $this->getRunDate($currentTime, $nth, false, $allowCurrentDate, $timeZone);
    }

    
    public function getPreviousRunDate($currentTime = 'now', int $nth = 0, bool $allowCurrentDate = false, $timeZone = null): DateTime
    {
        return $this->getRunDate($currentTime, $nth, true, $allowCurrentDate, $timeZone);
    }

    
    public function getMultipleRunDates(int $total, $currentTime = 'now', bool $invert = false, bool $allowCurrentDate = false, $timeZone = null): array
    {
        $matches = [];
        $max = max(0, $total);
        for ($i = 0; $i < $max; ++$i) {
            try {
                $matches[] = $this->getRunDate($currentTime, $i, $invert, $allowCurrentDate, $timeZone);
            } catch (RuntimeException $e) {
                break;
            }
        }

        return $matches;
    }

    
    public function getExpression($part = null): ?string
    {
        if (null === $part) {
            return implode(' ', $this->cronParts);
        }

        if (array_key_exists($part, $this->cronParts)) {
            return $this->cronParts[$part];
        }

        return null;
    }

    
    public function __toString(): string
    {
        return (string) $this->getExpression();
    }

    
    public function isDue($currentTime = 'now', $timeZone = null): ?bool
    {
        $timeZone = $this->determineTimeZone($currentTime, $timeZone);

        if ('now' === $currentTime) {
            $currentTime = new DateTime();
        } elseif ($currentTime instanceof DateTime) {
            $currentTime = clone $currentTime;
        } elseif ($currentTime instanceof DateTimeImmutable) {
            $currentTime = DateTime::createFromFormat('U', $currentTime->format('U'));
        } else {
            $currentTime = new DateTime($currentTime);
        }
        $currentTime->setTimezone(new DateTimeZone($timeZone));

        
        $currentTime->setTime((int) $currentTime->format('H'), (int) $currentTime->format('i'), 0);

        try {
            return $this->getNextRunDate($currentTime, 0, true)->getTimestamp() === $currentTime->getTimestamp();
        } catch (Exception $e) {
            return false;
        }
    }

    
    protected function getRunDate($currentTime = null, int $nth = 0, bool $invert = false, bool $allowCurrentDate = false, $timeZone = null): DateTime
    {
        $timeZone = $this->determineTimeZone($currentTime, $timeZone);

        if ($currentTime instanceof DateTime) {
            $currentDate = clone $currentTime;
        } elseif ($currentTime instanceof DateTimeImmutable) {
            $currentDate = DateTime::createFromFormat('U', $currentTime->format('U'));
        } else {
            $currentDate = new DateTime($currentTime ?: 'now');
        }

        $currentDate->setTimezone(new DateTimeZone($timeZone));
        $currentDate->setTime((int) $currentDate->format('H'), (int) $currentDate->format('i'), 0);

        $nextRun = clone $currentDate;

        
        $parts = [];
        $fields = [];
        foreach (self::$order as $position) {
            $part = $this->getExpression($position);
            if (null === $part || '*' === $part) {
                continue;
            }
            $parts[$position] = $part;
            $fields[$position] = $this->fieldFactory->getField($position);
        }

        if (isset($parts[2]) && isset($parts[4])) {
            $domExpression = sprintf('%s %s %s %s *', $this->getExpression(0), $this->getExpression(1), $this->getExpression(2), $this->getExpression(3));
            $dowExpression = sprintf('%s %s * %s %s', $this->getExpression(0), $this->getExpression(1), $this->getExpression(3), $this->getExpression(4));

            $domExpression = new self($domExpression);
            $dowExpression = new self($dowExpression);

            $domRunDates = $domExpression->getMultipleRunDates($nth + 1, $currentTime, $invert, $allowCurrentDate, $timeZone);
            $dowRunDates = $dowExpression->getMultipleRunDates($nth + 1, $currentTime, $invert, $allowCurrentDate, $timeZone);

            $combined = array_merge($domRunDates, $dowRunDates);
            usort($combined, function ($a, $b) {
                return $a->format('Y-m-d H:i:s') <=> $b->format('Y-m-d H:i:s');
            });

            return $combined[$nth];
        }

        
        for ($i = 0; $i < $this->maxIterationCount; ++$i) {
            foreach ($parts as $position => $part) {
                $satisfied = false;
                
                $field = $fields[$position];
                
                if (false === strpos($part, ',')) {
                    $satisfied = $field->isSatisfiedBy($nextRun, $part);
                } else {
                    foreach (array_map('trim', explode(',', $part)) as $listPart) {
                        if ($field->isSatisfiedBy($nextRun, $listPart)) {
                            $satisfied = true;

                            break;
                        }
                    }
                }

                
                if (!$satisfied) {
                    $field->increment($nextRun, $invert, $part);

                    continue 2;
                }
            }

            
            if ((!$allowCurrentDate && $nextRun == $currentDate) || --$nth > -1) {
                $this->fieldFactory->getField(0)->increment($nextRun, $invert, $parts[0] ?? null);

                continue;
            }

            return $nextRun;
        }

        
        throw new RuntimeException('Impossible CRON expression');
        
    }

    
    protected function determineTimeZone($currentTime, $timeZone): string
    {
        if (null !== $timeZone) {
            return $timeZone;
        }

        if ($currentTime instanceof DateTimeInterface) {
            return $currentTime->getTimeZone()->getName();
        }

        return date_default_timezone_get();
    }
}
