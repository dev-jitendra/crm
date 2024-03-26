<?php



declare(strict_types=1);

namespace Slim\Psr7;

use InvalidArgumentException;

use function array_merge;
use function is_array;
use function is_string;

class Header
{
    private string $originalName;

    private string $normalizedName;

    private array $values;

    
    public function __construct(string $originalName, string $normalizedName, array $values)
    {
        $this->originalName = $originalName;
        $this->normalizedName = $normalizedName;
        $this->values = $values;
    }

    
    public function getOriginalName(): string
    {
        return $this->originalName;
    }

    
    public function getNormalizedName(): string
    {
        return $this->normalizedName;
    }

    
    public function addValue(string $value): self
    {
        $this->values[] = $value;

        return $this;
    }

    
    public function addValues($values): self
    {
        if (is_string($values)) {
            return $this->addValue($values);
        }

        if (!is_array($values)) {
            throw new InvalidArgumentException('Parameter 1 of Header::addValues() should be a string or an array.');
        }

        $this->values = array_merge($this->values, $values);

        return $this;
    }

    
    public function getValues(): array
    {
        return $this->values;
    }
}
