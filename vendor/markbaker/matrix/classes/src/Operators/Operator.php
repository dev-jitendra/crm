<?php

namespace Matrix\Operators;

use Matrix\Matrix;
use Matrix\Exception;

abstract class Operator
{
    
    protected $matrix;

    
    protected $rows;

    
    protected $columns;

    
    public function __construct(Matrix $matrix)
    {
        $this->rows = $matrix->rows;
        $this->columns = $matrix->columns;
        $this->matrix = $matrix->toArray();
    }

    
    protected function validateMatchingDimensions(Matrix $matrix): void
    {
        if (($this->rows != $matrix->rows) || ($this->columns != $matrix->columns)) {
            throw new Exception('Matrices have mismatched dimensions');
        }
    }

    
    protected function validateReflectingDimensions(Matrix $matrix): void
    {
        if ($this->columns != $matrix->rows) {
            throw new Exception('Matrices have mismatched dimensions');
        }
    }

    
    public function result(): Matrix
    {
        return new Matrix($this->matrix);
    }
}
