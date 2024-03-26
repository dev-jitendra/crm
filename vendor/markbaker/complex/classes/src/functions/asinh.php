<?php


namespace Complex;


function asinh($complex): Complex
{
    $complex = Complex::validateComplexArgument($complex);

    if ($complex->isReal() && ($complex->getReal() > 1)) {
        return new Complex(\asinh($complex->getReal()));
    }

    $asinh = clone $complex;
    $asinh = $asinh->reverse()
        ->invertReal();
    $asinh = asin($asinh);
    return $asinh->reverse()
        ->invertImaginary();
}
