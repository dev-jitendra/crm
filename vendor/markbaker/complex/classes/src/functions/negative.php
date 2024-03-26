<?php


namespace Complex;


function negative($complex): Complex
{
    $complex = Complex::validateComplexArgument($complex);

    return new Complex(
        -1 * $complex->getReal(),
        -1 * $complex->getImaginary(),
        $complex->getSuffix()
    );
}
