<?php



namespace Matrix;


class Builder
{
    
    public static function createFilledMatrix($fillValue, $rows, $columns = null)
    {
        if ($columns === null) {
            $columns = $rows;
        }

        $rows = Matrix::validateRow($rows);
        $columns = Matrix::validateColumn($columns);

        return new Matrix(
            array_fill(
                0,
                $rows,
                array_fill(
                    0,
                    $columns,
                    $fillValue
                )
            )
        );
    }

    
    public static function createIdentityMatrix($dimensions)
    {
        $grid = static::createFilledMatrix(null, $dimensions)->toArray();

        for ($x = 0; $x < $dimensions; ++$x) {
            $grid[$x][$x] = 1;
        }

        return new Matrix($grid);
    }
}
