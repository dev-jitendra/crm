<?php


namespace Complex;


function asin($complex): Complex
{
    $complex = Complex::validateComplexArgument($complex);

    $square = multiply($complex, $complex);
    $invsqrt = new Complex(1.0);
    $invsqrt = subtract($invsqrt, $square);
    $invsqrt = sqrt($invsqrt);
    $adjust = new Complex(
        $invsqrt->getReal() - $complex->getImaginary(),
        $invsqrt->getImaginary() + $complex->getReal()
    );
    $log = ln($adjust);

    return new Complex(
        $log->getImaginary(),
        -1 * $log->getReal()
    );
}
