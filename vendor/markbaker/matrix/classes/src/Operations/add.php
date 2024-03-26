<?php



namespace Matrix;

use Matrix\Operators\Addition;


function add(...$matrixValues): Matrix
{
    if (count($matrixValues) < 2) {
        throw new Exception('Addition operation requires at least 2 arguments');
    }

    $matrix = array_shift($matrixValues);

    if (is_array($matrix)) {
        $matrix = new Matrix($matrix);
    }
    if (!$matrix instanceof Matrix) {
        throw new Exception('Addition arguments must be Matrix or array');
    }

    $result = new Addition($matrix);

    foreach ($matrixValues as $matrix) {
        $result->execute($matrix);
    }

    return $result->result();
}
