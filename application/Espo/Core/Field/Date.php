<?php


namespace Espo\Core\Field;

use Espo\Core\Field\DateTime\DateTimeable;

use DateTimeImmutable;
use DateTimeInterface;
use DateInterval;
use DateTimeZone;
use RuntimeException;


class Date implements DateTimeable
{
    private string $value;
    private DateTimeImmutable $dateTime;

    private const SYSTEM_FORMAT = 'Y-m-d';

    public function __construct(string $value)
    {
        if (!$value) {
            throw new RuntimeException("Empty value.");
        }

        $this->value = $value;

        $parsedValue = DateTimeImmutable::createFromFormat(
            '!' . self::SYSTEM_FORMAT,
            $value,
            new DateTimeZone('UTC')
        );

        if ($parsedValue === false) {
            throw new RuntimeException("Bad value.");
        }

        $this->dateTime = $parsedValue;

        if ($this->value !== $this->dateTime->format(self::SYSTEM_FORMAT)) {
            throw new RuntimeException("Bad value.");
        }
    }

    
    public function toString(): string
    {
        return $this->value;
    }

    
    public function toDateTime(): DateTimeImmutable
    {
        return $this->dateTime;
    }

    
    public function toTimestamp(): int
    {
        return $this->dateTime->getTimestamp();
    }

    
    public function getYear(): int
    {
        return (int) $this->dateTime->format('Y');
    }

    
    public function getMonth(): int
    {
        return (int) $this->dateTime->format('n');
    }

    
    public function getDay(): int
    {
        return (int) $this->dateTime->format('j');
    }

    
    public function getDayOfWeek(): int
    {
        return (int) $this->dateTime->format('w');
    }

    
    public function modify(string $modifier): self
    {
        
        $dateTime = $this->dateTime->modify($modifier);

        if (!$dateTime) {
            throw new RuntimeException("Modify failure.");
        }

        return self::fromDateTime($dateTime);
    }

    
    public function add(DateInterval $interval): self
    {
        $dateTime = $this->dateTime->add($interval);

        return self::fromDateTime($dateTime);
    }

    
    public function subtract(DateInterval $interval): self
    {
        $dateTime = $this->dateTime->sub($interval);

        return self::fromDateTime($dateTime);
    }

    
    public function addDays(int $days): self
    {
        $modifier = ($days >= 0 ? '+' : '-') . abs($days) . ' days';

        return $this->modify($modifier);
    }

    
    public function addMonths(int $months): self
    {
        $modifier = ($months >= 0 ? '+' : '-') . abs($months) . ' months';

        return $this->modify($modifier);
    }

    
    public function addYears(int $years): self
    {
        $modifier = ($years >= 0 ? '+' : '-') . abs($years) . ' years';

        return $this->modify($modifier);
    }

    
    public function diff(DateTimeable $other): DateInterval
    {
        return $this->toDateTime()->diff($other->toDateTime());
    }

    
    public function isGreaterThan(DateTimeable $other): bool
    {
        return $this->toDateTime() > $other->toDateTime();
    }

    
    public function isLessThan(DateTimeable $other): bool
    {
        return $this->toDateTime() < $other->toDateTime();
    }

    
    public function isEqualTo(DateTimeable $other): bool
    {
        return $this->toDateTime() == $other->toDateTime();
    }

    
    public static function createToday(?DateTimeZone $timezone = null): self
    {
        $now = new DateTimeImmutable();

        if ($timezone) {
            $now = $now->setTimezone($timezone);
        }

        return self::fromDateTime($now);
    }

    
    public static function fromString(string $value): self
    {
        return new self($value);
    }

    
    public static function fromDateTime(DateTimeInterface $dateTime): self
    {
        $value = $dateTime->format(self::SYSTEM_FORMAT);

        return new self($value);
    }

    
    public function getString(): string
    {
        return $this->toString();
    }

    
    public function getDateTime(): DateTimeImmutable
    {
        return $this->toDateTime();
    }

    
    public function getTimestamp(): int
    {
        return $this->toTimestamp();
    }
}
