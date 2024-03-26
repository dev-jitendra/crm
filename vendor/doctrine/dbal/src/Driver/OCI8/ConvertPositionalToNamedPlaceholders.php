<?php

namespace Doctrine\DBAL\Driver\OCI8;

use Doctrine\DBAL\SQL\Parser\Visitor;

use function count;
use function implode;


final class ConvertPositionalToNamedPlaceholders implements Visitor
{
    
    private array $buffer = [];

    
    private array $parameterMap = [];

    public function acceptOther(string $sql): void
    {
        $this->buffer[] = $sql;
    }

    public function acceptPositionalParameter(string $sql): void
    {
        $position = count($this->parameterMap) + 1;
        $param    = ':param' . $position;

        $this->parameterMap[$position] = $param;

        $this->buffer[] = $param;
    }

    public function acceptNamedParameter(string $sql): void
    {
        $this->buffer[] = $sql;
    }

    public function getSQL(): string
    {
        return implode('', $this->buffer);
    }

    
    public function getParameterMap(): array
    {
        return $this->parameterMap;
    }
}
