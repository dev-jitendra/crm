<?php


namespace Espo\Core\Field;

use Espo\Core\Currency\CalculatorUtil;

use RuntimeException;
use InvalidArgumentException;


class Currency
{
    private string $amount;
    private string $code;

    
    public function __construct($amount, string $code)
    {
        if (!is_string($amount) && !is_float($amount)) {
            throw new InvalidArgumentException();
        }

        if (strlen($code) !== 3) {
            throw new RuntimeException("Bad currency code.");
        }

        if (is_float($amount)) {
            $amount = (string) $amount;
        }

        $this->amount = $amount;
        $this->code = $code;
    }

    
    public function getAmountAsString(): string
    {
        return $this->amount;
    }

    
    public function getAmount(): float
    {
        return (float) $this->amount;
    }

    
    public function getCode(): string
    {
        return $this->code;
    }

    
    public function add(self $value): self
    {
        if ($this->getCode() !== $value->getCode()) {
            throw new RuntimeException("Can't add a currency value with a different code.");
        }

        $amount = CalculatorUtil::add(
            $this->getAmountAsString(),
            $value->getAmountAsString()
        );

        return new self($amount, $this->getCode());
    }

    
    public function subtract(self $value): self
    {
        if ($this->getCode() !== $value->getCode()) {
            throw new RuntimeException("Can't subtract a currency value with a different code.");
        }

        $amount = CalculatorUtil::subtract(
            $this->getAmountAsString(),
            $value->getAmountAsString()
        );

        return new self($amount, $this->getCode());
    }

    
    public function multiply(float|int $multiplier): self
    {
        $amount = CalculatorUtil::multiply(
            $this->getAmountAsString(),
            (string) $multiplier
        );

        return new self($amount, $this->getCode());
    }

    
    public function divide(float|int $divider): self
    {
        $amount = CalculatorUtil::divide(
            $this->getAmountAsString(),
            (string) $divider
        );

        return new self($amount, $this->getCode());
    }

    
    public function round(int $precision = 0): self
    {
        $amount = CalculatorUtil::round($this->getAmountAsString(), $precision);

        return new self($amount, $this->getCode());
    }

    
    public function compare(self $value): int
    {
        if ($this->getCode() !== $value->getCode()) {
            throw new RuntimeException("Can't compare currencies with different codes.");
        }

        return CalculatorUtil::compare(
            $this->getAmountAsString(),
            $value->getAmountAsString()
        );
    }

    
    public function isNegative(): bool
    {
        return $this->compare(self::create(0.0, $this->code)) === -1;
    }

    
    public static function create($amount, string $code): self
    {
        return new self($amount, $code);
    }
}
