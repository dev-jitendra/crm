<?php

namespace Doctrine\DBAL;

use Doctrine\DBAL\ArrayParameters\Exception\MissingNamedParameter;
use Doctrine\DBAL\ArrayParameters\Exception\MissingPositionalParameter;
use Doctrine\DBAL\SQL\Parser\Visitor;
use Doctrine\DBAL\Types\Type;

use function array_fill;
use function array_key_exists;
use function count;
use function implode;
use function substr;

final class ExpandArrayParameters implements Visitor
{
    
    private array $originalParameters;

    
    private array $originalTypes;

    private int $originalParameterIndex = 0;

    
    private array $convertedSQL = [];

    
    private array $convertedParameters = [];

    
    private array $convertedTypes = [];

    
    public function __construct(array $parameters, array $types)
    {
        $this->originalParameters = $parameters;
        $this->originalTypes      = $types;
    }

    public function acceptPositionalParameter(string $sql): void
    {
        $index = $this->originalParameterIndex;

        if (! array_key_exists($index, $this->originalParameters)) {
            throw MissingPositionalParameter::new($index);
        }

        $this->acceptParameter($index, $this->originalParameters[$index]);

        $this->originalParameterIndex++;
    }

    public function acceptNamedParameter(string $sql): void
    {
        $name = substr($sql, 1);

        if (! array_key_exists($name, $this->originalParameters)) {
            throw MissingNamedParameter::new($name);
        }

        $this->acceptParameter($name, $this->originalParameters[$name]);
    }

    public function acceptOther(string $sql): void
    {
        $this->convertedSQL[] = $sql;
    }

    public function getSQL(): string
    {
        return implode('', $this->convertedSQL);
    }

    
    public function getParameters(): array
    {
        return $this->convertedParameters;
    }

    
    private function acceptParameter($key, $value): void
    {
        if (! isset($this->originalTypes[$key])) {
            $this->convertedSQL[]        = '?';
            $this->convertedParameters[] = $value;

            return;
        }

        $type = $this->originalTypes[$key];

        if (
            $type !== Connection::PARAM_INT_ARRAY
            && $type !== Connection::PARAM_STR_ARRAY
            && $type !== Connection::PARAM_ASCII_STR_ARRAY
        ) {
            $this->appendTypedParameter([$value], $type);

            return;
        }

        if (count($value) === 0) {
            $this->convertedSQL[] = 'NULL';

            return;
        }

        $this->appendTypedParameter($value, $type - Connection::ARRAY_PARAM_OFFSET);
    }

    
    public function getTypes(): array
    {
        return $this->convertedTypes;
    }

    
    private function appendTypedParameter(array $values, $type): void
    {
        $this->convertedSQL[] = implode(', ', array_fill(0, count($values), '?'));

        $index = count($this->convertedParameters);

        foreach ($values as $value) {
            $this->convertedParameters[]  = $value;
            $this->convertedTypes[$index] = $type;

            $index++;
        }
    }
}
