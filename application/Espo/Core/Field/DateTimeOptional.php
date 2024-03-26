<?php


namespace Espo\Core\Field;

use Espo\Core\Field\DateTime\DateTimeable;

use DateTimeImmutable;
use DateTimeInterface;
use DateInterval;
use DateTimeZone;
use RuntimeException;


class DateTimeOptional implements DateTimeable
{
    private ?DateTime $dateTimeValue = null;
    private ?Date $dateValue = null;

    private const SYSTEM_FORMAT = 'Y-m-d H:i:s';
    private const SYSTEM_FORMAT_DATE = 'Y-m-d';

    public function __construct(string $value)
    {
        if (self::isStringDateTime($value)) {
            $this->dateTimeValue = new DateTime($value);
        }
        else {
            $this->dateValue = new Date($value);
        }
    }

    
    public static function fromString(string $value): self
    {
        return new self($value);
    }

    
    public static function fromDateTimeString(string $value): self
    {
        if (!self::isStringDateTime($value)) {
            throw new RuntimeException("Bad value.");
        }

        return self::fromString($value);
    }

    
    public function toString(): string
    {
        return $this->getActualValue()->toString();
    }

    
    public function toDateTime(): DateTimeImmutable
    {
        return $this->getActualValue()->toDateTime();
    }

    
    public function toTimestamp(): int
    {
        return $this->getActualValue()->toDateTime()->getTimestamp();
    }

    
    public function getYear(): int
    {
        return $this->getActualValue()->getYear();
    }

    
    public function getMonth(): int
    {
        return $this->getActualValue()->getMonth();
    }

    
    public function getDay(): int
    {
        return $this->getActualValue()->getDay();
    }

    
    public function getDayOfWeek(): int
    {
        return $this->getActualValue()->getDayOfWeek();
    }

    
    public function getHour(): int
    {
        if ($this->isAllDay()) {
            return 0;
        }

        
        $value = $this->getActualValue();

        return $value->getHour();
    }

    
    public function getMinute(): int
    {
        if ($this->isAllDay()) {
            return 0;
        }

        
        $value = $this->getActualValue();

        return $value->getMinute();
    }

    
    public function getSecond(): int
    {
        if ($this->isAllDay()) {
            return 0;
        }

        
        $value = $this->getActualValue();

        return $value->getSecond();
    }

    
    public function isAllDay(): bool
    {
        return $this->dateValue !== null;
    }

    
    public function getTimezone(): DateTimeZone
    {
        return $this->toDateTime()->getTimezone();
    }

    private function getActualValue(): Date|DateTime
    {
        
        return $this->dateValue ?? $this->dateTimeValue;
    }

    
    public function withTimezone(DateTimeZone $timezone): self
    {
        if ($this->isAllDay()) {
            $dateTime = $this->getActualValue()->toDateTime()->setTimezone($timezone);

            return self::fromDateTime($dateTime);
        }

        
        $value = $this->getActualValue();

        $dateTime = $value->withTimezone($timezone)->toDateTime();

        return self::fromDateTime($dateTime);
    }

    
    public function withTime(?int $hour, ?int $minute, ?int $second = 0): self
    {
        if ($this->isAllDay()) {
            $dateTime = DateTime::fromDateTime($this->getActualValue()->toDateTime())
                ->withTime($hour, $minute, $second);

            return self::fromDateTime($dateTime->toDateTime());
        }

        
        $value = $this->getActualValue();

        $dateTime = $value->withTime($hour, $minute, $second);

        return self::fromDateTime($dateTime->toDateTime());
    }

    
    public function modify(string $modifier): self
    {
        if ($this->isAllDay()) {
            assert($this->dateValue !== null);

            return self::fromDateTimeAllDay(
                $this->dateValue->modify($modifier)->toDateTime()
            );
        }

        assert($this->dateTimeValue !== null);

        return self::fromDateTime(
            $this->dateTimeValue->modify($modifier)->toDateTime()
        );
    }

    
    public function add(DateInterval $interval): self
    {
        if ($this->isAllDay()) {
            assert($this->dateValue !== null);

            return self::fromDateTimeAllDay(
                $this->dateValue->add($interval)->toDateTime()
            );
        }

        assert($this->dateTimeValue !== null);

        return self::fromDateTime(
            $this->dateTimeValue->add($interval)->toDateTime()
        );
    }

    
    public function subtract(DateInterval $interval): self
    {
        if ($this->isAllDay()) {
            assert($this->dateValue !== null);

            return self::fromDateTimeAllDay(
                $this->dateValue->subtract($interval)->toDateTime()
            );
        }

        assert($this->dateTimeValue !== null);

        return self::fromDateTime(
            $this->dateTimeValue->subtract($interval)->toDateTime()
        );
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

    
    public static function createToday(?DateTimeZone $timezone = null): self
    {
        $now = new DateTimeImmutable();

        if ($timezone) {
            $now = $now->setTimezone($timezone);
        }

        return self::fromDateTimeAllDay($now);
    }

    
    public static function fromDateString(string $value): self
    {
        if (self::isStringDateTime($value)) {
            throw new RuntimeException("Bad value.");
        }

        return self::fromString($value);
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

        $obj = self::fromString($utcValue);

        assert($obj->dateTimeValue !== null);

        $obj->dateTimeValue = $obj->dateTimeValue->withTimezone($dateTime->getTimezone());

        return $obj;
    }

    
    public static function fromDateTimeAllDay(DateTimeInterface $dateTime): self
    {
        $value = $dateTime->format(self::SYSTEM_FORMAT_DATE);

        return new self($value);
    }

    private static function isStringDateTime(string $value): bool
    {
        if (strlen($value) > 10) {
            return true;
        }

        return false;
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
