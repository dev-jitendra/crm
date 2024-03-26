<?php



namespace Matrix;

use Matrix\Operators\Multiplication;


function multiply(...$matrixValues): Matrix
{
    if (count($matrixValues) < 2) {
        throw new Exception('Multiplication operation requires at least 2 arguments');
    }

    $matrix = array_shift($matrixValues);

    if (is_array($matrix)) {
        $matrix = new Matrix($matrix);
    }
    if (!$matrix instanceof Matrix) {
        throw new Exception('Multiplication arguments must be Matrix or array');
    }

    $result = new Multiplication($matrix);

    foreach ($matrixValues as $matrix) {
        $result->execute($matrix);
    }

    return $result->result();
}
