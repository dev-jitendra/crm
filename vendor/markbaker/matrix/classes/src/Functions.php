<?php

namespace Matrix;

class Functions
{
    
    private static function getAdjoint(Matrix $matrix)
    {
        return self::transpose(
            self::getCofactors($matrix)
        );
    }

    
    public static function adjoint(Matrix $matrix)
    {
        if (!$matrix->isSquare()) {
            throw new Exception('Adjoint can only be calculated for a square matrix');
        }

        return self::getAdjoint($matrix);
    }

    
    private static function getCofactors(Matrix $matrix)
    {
        $cofactors = self::getMinors($matrix);
        $dimensions = $matrix->rows;

        $cof = 1;
        for ($i = 0; $i < $dimensions; ++$i) {
            $cofs = $cof;
            for ($j = 0; $j < $dimensions; ++$j) {
                $cofactors[$i][$j] *= $cofs;
                $cofs = -$cofs;
            }
            $cof = -$cof;
        }

        return new Matrix($cofactors);
    }

    
    public static function cofactors(Matrix $matrix)
    {
        if (!$matrix->isSquare()) {
            throw new Exception('Cofactors can only be calculated for a square matrix');
        }

        return self::getCofactors($matrix);
    }

    
    private static function getDeterminantSegment(Matrix $matrix, $row, $column)
    {
        $tmpMatrix = $matrix->toArray();
        unset($tmpMatrix[$row]);
        array_walk(
            $tmpMatrix,
            function (&$row) use ($column) {
                unset($row[$column]);
            }
        );

        return self::getDeterminant(new Matrix($tmpMatrix));
    }

    
    private static function getDeterminant(Matrix $matrix)
    {
        $dimensions = $matrix->rows;
        $determinant = 0;

        switch ($dimensions) {
            case 1:
                $determinant = $matrix->getValue(1, 1);
                break;
            case 2:
                $determinant = $matrix->getValue(1, 1) * $matrix->getValue(2, 2) -
                    $matrix->getValue(1, 2) * $matrix->getValue(2, 1);
                break;
            default:
                for ($i = 1; $i <= $dimensions; ++$i) {
                    $det = $matrix->getValue(1, $i) * self::getDeterminantSegment($matrix, 0, $i - 1);
                    if (($i % 2) == 0) {
                        $determinant -= $det;
                    } else {
                        $determinant += $det;
                    }
                }
                break;
        }

        return $determinant;
    }

    
    public static function determinant(Matrix $matrix)
    {
        if (!$matrix->isSquare()) {
            throw new Exception('Determinant can only be calculated for a square matrix');
        }

        return self::getDeterminant($matrix);
    }

    
    public static function diagonal(Matrix $matrix)
    {
        if (!$matrix->isSquare()) {
            throw new Exception('Diagonal can only be extracted from a square matrix');
        }

        $dimensions = $matrix->rows;
        $grid = Builder::createFilledMatrix(0, $dimensions, $dimensions)
            ->toArray();

        for ($i = 0; $i < $dimensions; ++$i) {
            $grid[$i][$i] = $matrix->getValue($i + 1, $i + 1);
        }

        return new Matrix($grid);
    }

    
    public static function antidiagonal(Matrix $matrix)
    {
        if (!$matrix->isSquare()) {
            throw new Exception('Anti-Diagonal can only be extracted from a square matrix');
        }

        $dimensions = $matrix->rows;
        $grid = Builder::createFilledMatrix(0, $dimensions, $dimensions)
            ->toArray();

        for ($i = 0; $i < $dimensions; ++$i) {
            $grid[$i][$dimensions - $i - 1] = $matrix->getValue($i + 1, $dimensions - $i);
        }

        return new Matrix($grid);
    }

    
    public static function identity(Matrix $matrix)
    {
        if (!$matrix->isSquare()) {
            throw new Exception('Identity can only be created for a square matrix');
        }

        $dimensions = $matrix->rows;

        return Builder::createIdentityMatrix($dimensions);
    }

    
    public static function inverse(Matrix $matrix)
    {
        if (!$matrix->isSquare()) {
            throw new Exception('Inverse can only be calculated for a square matrix');
        }

        $determinant = self::getDeterminant($matrix);
        if ($determinant == 0.0) {
            throw new Exception('Inverse can only be calculated for a matrix with a non-zero determinant');
        }

        if ($matrix->rows == 1) {
            return new Matrix([[1 / $matrix->getValue(1, 1)]]);
        }

        return self::getAdjoint($matrix)
            ->multiply(1 / $determinant);
    }

    
    protected static function getMinors(Matrix $matrix)
    {
        $minors = $matrix->toArray();
        $dimensions = $matrix->rows;
        if ($dimensions == 1) {
            return $minors;
        }

        for ($i = 0; $i < $dimensions; ++$i) {
            for ($j = 0; $j < $dimensions; ++$j) {
                $minors[$i][$j] = self::getDeterminantSegment($matrix, $i, $j);
            }
        }

        return $minors;
    }

    
    public static function minors(Matrix $matrix)
    {
        if (!$matrix->isSquare()) {
            throw new Exception('Minors can only be calculated for a square matrix');
        }

        return new Matrix(self::getMinors($matrix));
    }

    
    public static function trace(Matrix $matrix)
    {
        if (!$matrix->isSquare()) {
            throw new Exception('Trace can only be extracted from a square matrix');
        }

        $dimensions = $matrix->rows;
        $result = 0;
        for ($i = 1; $i <= $dimensions; ++$i) {
            $result += $matrix->getValue($i, $i);
        }

        return $result;
    }

    
    public static function transpose(Matrix $matrix)
    {
        $array = array_values(array_merge([null], $matrix->toArray()));
        $grid = call_user_func_array(
            'array_map',
            $array
        );

        return new Matrix($grid);
    }
}
