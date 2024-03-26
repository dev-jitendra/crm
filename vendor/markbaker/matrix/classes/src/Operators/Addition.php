<?php

namespace Matrix\Operators;

use Matrix\Matrix;
use Matrix\Exception;

class Addition extends Operator
{
    
    public function execute($value): Operator
    {
        if (is_array($value)) {
            $value = new Matrix($value);
        }

        if (is_object($value) && ($value instanceof Matrix)) {
            return $this->addMatrix($value);
        } elseif (is_numeric($value)) {
            return $this->addScalar($value);
        }

        throw new Exception('Invalid argument for addition');
    }

    
    protected function addScalar($value): Operator
    {
        for ($row = 0; $row < $this->rows; ++$row) {
            for ($column = 0; $column < $this->columns; ++$column) {
                $this->matrix[$row][$column] += $value;
            }
        }

        return $this;
    }

    
    protected function addMatrix(Matrix $value): Operator
    {
        $this->validateMatchingDimensions($value);

        for ($row = 0; $row < $this->rows; ++$row) {
            for ($column = 0; $column < $this->columns; ++$column) {
                $this->matrix[$row][$column] += $value->getValue($row + 1, $column + 1);
            }
        }

        return $this;
    }
}
