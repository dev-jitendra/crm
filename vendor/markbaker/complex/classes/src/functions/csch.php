<?php


namespace Complex;


function csch($complex): Complex
{
    $complex = Complex::validateComplexArgument($complex);

    if ($complex->getReal() == 0.0 && $complex->getImaginary() == 0.0) {
        return new Complex(INF);
    }

    return inverse(sinh($complex));
}
