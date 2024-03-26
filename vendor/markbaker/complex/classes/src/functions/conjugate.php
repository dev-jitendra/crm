<?php


namespace Complex;


function conjugate($complex): Complex
{
    $complex = Complex::validateComplexArgument($complex);

    return new Complex(
        $complex->getReal(),
        -1 * $complex->getImaginary(),
        $complex->getSuffix()
    );
}
