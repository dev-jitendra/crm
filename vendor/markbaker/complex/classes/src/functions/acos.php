<?php


namespace Complex;


function acos($complex): Complex
{
    $complex = Complex::validateComplexArgument($complex);

    $square = clone $complex;
    $square = multiply($square, $complex);
    $invsqrt = new Complex(1.0);
    $invsqrt = subtract($invsqrt, $square);
    $invsqrt = sqrt($invsqrt);
    $adjust = new Complex(
        $complex->getReal() - $invsqrt->getImaginary(),
        $complex->getImaginary() + $invsqrt->getReal()
    );
    $log = ln($adjust);

    return new Complex(
        $log->getImaginary(),
        -1 * $log->getReal()
    );
}
