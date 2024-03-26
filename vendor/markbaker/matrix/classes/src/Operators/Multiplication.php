<?php

namespace Matrix\Operators;

use Matrix\Matrix;
use \Matrix\Builder;
use Matrix\Exception;

class Multiplication extends Operator
{
    
    public function execute($value): Operator
    {
        if (is_array($value)) {
            $value = new Matrix($value);
        }

        if (is_object($value) && ($value instanceof Matrix)) {
            return $this->multiplyMatrix($value);
        } elseif (is_numeric($value)) {
            return $this->multiplyScalar($value);
        }

        throw new Exception('Invalid argument for multiplication');
    }

    
    protected function multiplyScalar($value): Operator
    {
        for ($row = 0; $row < $this->rows; ++$row) {
            for ($column = 0; $column < $this->columns; ++$column) {
                $this->matrix[$row][$column] *= $value;
            }
        }

        return $this;
    }

    
    protected function multiplyMatrix(Matrix $value): Operator
    {
        $this->validateReflectingDimensions($value);

        $newRows = $this->rows;
        $newColumns = $value->columns;
        $matrix = Builder::createFilledMatrix(0, $newRows, $newColumns)
            ->toArray();
        for ($row = 0; $row < $newRows; ++$row) {
            for ($column = 0; $column < $newColumns; ++$column) {
                $columnData = $value->getColumns($column + 1)->toArray();
                foreach ($this->matrix[$row] as $key => $valueData) {
                    $matrix[$row][$column] += $valueData * $columnData[$key][0];
                }
            }
        }
        $this->matrix = $matrix;

        return $this;
    }
}
