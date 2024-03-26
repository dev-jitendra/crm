<?php


namespace Espo\Core\Field;

use Espo\Core\Field\DateTime\DateTimeable;

use DateTimeImmutable;
use DateTimeInterface;
use DateInterval;
use DateTimeZone;
use RuntimeException;


class DateTime implements DateTimeable
{
    private string $value;
    private DateTimeImmutable $dateTime;

    private const SYSTEM_FORMAT = 'Y-m-d H:i:s';

    public function __construct(string $value)
    {
        if (!$value) {
            throw new RuntimeException("Empty value.");
        }

        $normValue = strlen($value) === 16 ? $value . ':00' : $value;

        $this->value = $normValue;

        $parsedValue = DateTimeImmutable::createFromFormat(
            self::SYSTEM_FORMAT,
            $normValue,
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

    
    public function getHour(): int
    {
        return (int) $this->dateTime->format('G');
    }

    
    public function getMinute(): int
    {
        return (int) $this->dateTime->format('i');
    }

    
    public function getSecond(): int
    {
        return (int) $this->dateTime->format('s');
    }

    
    public function getTimezone(): DateTimeZone
    {
        return $this->dateTime->getTimezone();
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

    
    public function addHours(int $hours): self
    {
        $modifier = ($hours >= 0 ? '+' : '-') . abs($hours) . ' hours';

        return $this->modify($modifier);
    }

    
    public function addMinutes(int $minutes): self
    {
        $modifier = ($minutes >= 0 ? '+' : '-') . abs($minutes) . ' minutes';

        return $this->modify($modifier);
    }

    
    public function addSeconds(int $seconds): self
    {
        $modifier = ($seconds >= 0 ? '+' : '-') . abs($seconds) . ' seconds';

        return $this->modify($modifier);
    }

    
    public function diff(DateTimeable $other): DateInterval
    {
        return $this->toDateTime()->diff($other->toDateTime());
    }

    
    public function withTimezone(DateTimeZone $timezone): self
    {
        $dateTime = $this->dateTime->setTimezone($timezone);

        return self::fromDateTime($dateTime);
    }

    
    public function withTime(?int $hour, ?int $minute, ?int $second = 0): self
    {
        $dateTime = $this->dateTime->setTime(
            $hour ?? $this->getHour(),
            $minute ?? $this->getMinute(),
            $second ?? $this->getSecond()
        );

        return self::fromDateTime($dateTime);
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

    
    public static function createNow(): self
    {
        return self::fromDateTime(new DateTimeImmutable());
    }

    
    public static function fromString(string $value): self
    {
        return new self($value);
    }

    
    public static function fromTimestamp(int $timestamp): self
    {
        $dateTime = (new DateTimeImmutable)->setTimestamp($timestamp);

        return self::fromDateTime($dateTime);
    }

    
    public static function fromDateTime(DateTimeInterface $dateTime): self
    {
        
        $value = DateTimeImmutable::createFromFormat(
            self::SYSTEM_FORMAT,
            $dateTime->format(self::SYSTEM_FORMAT),
            $dateTime->getTimezone()
        );

        $utcValue = $value
             ->setTimezone(new DateTimeZone('UTC'))
             ->format(self::SYSTEM_FORMAT);

        $obj = new self($utcValue);

        $obj->dateTime = $obj->dateTime->setTimezone($dateTime->getTimezone());

        return $obj;
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
