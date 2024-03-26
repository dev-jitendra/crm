<?php


namespace Complex;


function atanh($complex): Complex
{
    $complex = Complex::validateComplexArgument($complex);

    if ($complex->isReal()) {
        $real = $complex->getReal();
        if ($real >= -1.0 && $real <= 1.0) {
            return new Complex(\atanh($real));
        } else {
            return new Complex(\atanh(1 / $real), (($real < 0.0) ? M_PI_2 : -1 * M_PI_2));
        }
    }

    $iComplex = clone $complex;
    $iComplex = $iComplex->invertImaginary()
        ->reverse();
    return atan($iComplex)
        ->invertReal()
        ->reverse();
}
