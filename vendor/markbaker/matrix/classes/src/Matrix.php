<?php



namespace Matrix;

use Generator;
use Matrix\Decomposition\LU;
use Matrix\Decomposition\QR;


class Matrix
{
    protected $rows;
    protected $columns;
    protected $grid = [];

    
    final public function __construct(array $grid)
    {
        $this->buildFromArray(array_values($grid));
    }

    
    protected function buildFromArray(array $grid): void
    {
        $this->rows = count($grid);
        $columns = array_reduce(
            $grid,
            function ($carry, $value) {
                return max($carry, is_array($value) ? count($value) : 1);
            }
        );
        $this->columns = $columns;

        array_walk(
            $grid,
            function (&$value) use ($columns) {
                if (!is_array($value)) {
                    $value = [$value];
                }
                $value = array_pad(array_values($value), $columns, null);
            }
        );

        $this->grid = $grid;
    }

    
    public static function validateRow(int $row): int
    {
        if ((!is_numeric($row)) || (intval($row) < 1)) {
            throw new Exception('Invalid Row');
        }

        return (int)$row;
    }

    
    public static function validateColumn(int $column): int
    {
        if ((!is_numeric($column)) || (intval($column) < 1)) {
            throw new Exception('Invalid Column');
        }

        return (int)$column;
    }

    
    protected function validateRowInRange(int $row): int
    {
        $row = static::validateRow($row);
        if ($row > $this->rows) {
            throw new Exception('Requested Row exceeds matrix size');
        }

        return $row;
    }

    
    protected function validateColumnInRange(int $column): int
    {
        $column = static::validateColumn($column);
        if ($column > $this->columns) {
            throw new Exception('Requested Column exceeds matrix size');
        }

        return $column;
    }

    
    public function getRows(int $row, int $rowCount = 1): Matrix
    {
        $row = $this->validateRowInRange($row);
        if ($rowCount === 0) {
            $rowCount = $this->rows - $row + 1;
        }

        return new static(array_slice($this->grid, $row - 1, (int)$rowCount));
    }

    
    public function getColumns(int $column, int $columnCount = 1): Matrix
    {
        $column = $this->validateColumnInRange($column);
        if ($columnCount < 1) {
            $columnCount = $this->columns + $columnCount - $column + 1;
        }

        $grid = [];
        for ($i = $column - 1; $i < $column + $columnCount - 1; ++$i) {
            $grid[] = array_column($this->grid, $i);
        }

        return (new static($grid))->transpose();
    }

    
    public function dropRows(int $row, int $rowCount = 1): Matrix
    {
        $this->validateRowInRange($row);
        if ($rowCount === 0) {
            $rowCount = $this->rows - $row + 1;
        }

        $grid = $this->grid;
        array_splice($grid, $row - 1, (int)$rowCount);

        return new static($grid);
    }

    
    public function dropColumns(int $column, int $columnCount = 1): Matrix
    {
        $this->validateColumnInRange($column);
        if ($columnCount < 1) {
            $columnCount = $this->columns + $columnCount - $column + 1;
        }

        $grid = $this->grid;
        array_walk(
            $grid,
            function (&$row) use ($column, $columnCount) {
                array_splice($row, $column - 1, (int)$columnCount);
            }
        );

        return new static($grid);
    }

    
    public function getValue(int $row, int $column)
    {
        $row = $this->validateRowInRange($row);
        $column = $this->validateColumnInRange($column);

        return $this->grid[$row - 1][$column - 1];
    }

    
    public function rows(): Generator
    {
        foreach ($this->grid as $i => $row) {
            yield $i + 1 => ($this->columns == 1)
                ? $row[0]
                : new static([$row]);
        }
    }

    
    public function columns(): Generator
    {
        for ($i = 0; $i < $this->columns; ++$i) {
            yield $i + 1 => ($this->rows == 1)
                ? $this->grid[0][$i]
                : new static(array_column($this->grid, $i));
        }
    }

    
    public function isSquare(): bool
    {
        return $this->rows === $this->columns;
    }

    
    public function isVector(): bool
    {
        return $this->rows === 1 || $this->columns === 1;
    }

    
    public function toArray(): array
    {
        return $this->grid;
    }

    
    public function solve(Matrix $B): Matrix
    {
        if ($this->columns === $this->rows) {
            return (new LU($this))->solve($B);
        }

        return (new QR($this))->solve($B);
    }

    protected static $getters = [
        'rows',
        'columns',
    ];

    
    public function __get(string $propertyName)
    {
        $propertyName = strtolower($propertyName);

        
        if (in_array($propertyName, self::$getters)) {
            return $this->$propertyName;
        }

        throw new Exception('Property does not exist');
    }

    protected static $functions = [
        'adjoint',
        'antidiagonal',
        'cofactors',
        'determinant',
        'diagonal',
        'identity',
        'inverse',
        'minors',
        'trace',
        'transpose',
    ];

    protected static $operations = [
        'add',
        'subtract',
        'multiply',
        'divideby',
        'divideinto',
        'directsum',
    ];

    
    public function __call(string $functionName, $arguments)
    {
        $functionName = strtolower(str_replace('_', '', $functionName));

        if (in_array($functionName, self::$functions, true) || in_array($functionName, self::$operations, true)) {
            $functionName = "\\" . __NAMESPACE__ . "\\{$functionName}";
            if (is_callable($functionName)) {
                $arguments = array_values(array_merge([$this], $arguments));
                return call_user_func_array($functionName, $arguments);
            }
        }
        throw new Exception('Function or Operation does not exist');
    }
}
