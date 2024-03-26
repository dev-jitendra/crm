<?php


namespace Espo\Core\Select\Text\FullTextSearch;

use Espo\ORM\Query\Part\Expression;

use InvalidArgumentException;


class Data
{
    
    private array $fieldList;
    
    private array $columnList;
    
    private string $mode;

    
    public function __construct(private Expression $expression, array $fieldList, array $columnList, string $mode)
    {
        $this->fieldList = $fieldList;
        $this->columnList = $columnList;
        $this->mode = $mode;

        if (!in_array($mode, [Mode::NATURAL_LANGUAGE, Mode::BOOLEAN])) {
            throw new InvalidArgumentException("Bad mode.");
        }
    }

    public function getExpression(): Expression
    {
        return $this->expression;
    }

    
    public function getFieldList(): array
    {
        return $this->fieldList;
    }

    
    public function getColumnList(): array
    {
        return $this->columnList;
    }

    
    public function getMode(): string
    {
        return $this->mode;
    }
}
