<?php



namespace Matrix;

use Matrix\Operators\Subtraction;


function subtract(...$matrixValues): Matrix
{
    if (count($matrixValues) < 2) {
        throw new Exception('Subtraction operation requires at least 2 arguments');
    }

    $matrix = array_shift($matrixValues);

    if (is_array($matrix)) {
        $matrix = new Matrix($matrix);
    }
    if (!$matrix instanceof Matrix) {
        throw new Exception('Subtraction arguments must be Matrix or array');
    }

    $result = new Subtraction($matrix);

    foreach ($matrixValues as $matrix) {
        $result->execute($matrix);
    }

    return $result->result();
}
