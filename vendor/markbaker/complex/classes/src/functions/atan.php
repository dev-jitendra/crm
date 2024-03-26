<?php


namespace Complex;





function atan($complex): Complex
{
    $complex = Complex::validateComplexArgument($complex);

    if ($complex->isReal()) {
        return new Complex(\atan($complex->getReal()));
    }

    $t1Value = new Complex(-1 * $complex->getImaginary(), $complex->getReal());
    $uValue = new Complex(1, 0);

    $d1Value = clone $uValue;
    $d1Value = subtract($d1Value, $t1Value);
    $d2Value = add($t1Value, $uValue);
    $uResult = $d1Value->divideBy($d2Value);
    $uResult = ln($uResult);

    return new Complex(
        (($uResult->getImaginary() == M_PI) ? -M_PI : $uResult->getImaginary()) * -0.5,
        $uResult->getReal() * 0.5,
        $complex->getSuffix()
    );
}
