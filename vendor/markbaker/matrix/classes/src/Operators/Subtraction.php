<?php

namespace Matrix\Operators;

use Matrix\Matrix;
use Matrix\Exception;

class Subtraction extends Operator
{
    
    public function execute($value): Operator
    {
        if (is_array($value)) {
            $value = new Matrix($value);
        }

        if (is_object($value) && ($value instanceof Matrix)) {
            return $this->subtractMatrix($value);
        } elseif (is_numeric($value)) {
            return $this->subtractScalar($value);
        }

        throw new Exception('Invalid argument for subtraction');
    }

    
    protected function subtractScalar($value): Operator
    {
        for ($row = 0; $row < $this->rows; ++$row) {
            for ($column = 0; $column < $this->columns; ++$column) {
                $this->matrix[$row][$column] -= $value;
            }
        }

        return $this;
    }

    
    protected function subtractMatrix(Matrix $value): Operator
    {
        $this->validateMatchingDimensions($value);

        for ($row = 0; $row < $this->rows; ++$row) {
            for ($column = 0; $column < $this->columns; ++$column) {
                $this->matrix[$row][$column] -= $value->getValue($row + 1, $column + 1);
            }
        }

        return $this;
    }
}
