<?php


namespace Complex;


function tanh($complex): Complex
{
    $complex = Complex::validateComplexArgument($complex);
    $real = $complex->getReal();
    $imaginary = $complex->getImaginary();
    $divisor = \cos($imaginary) * \cos($imaginary) + \sinh($real) * \sinh($real);
    if ($divisor == 0.0) {
        throw new \InvalidArgumentException('Division by zero');
    }

    return new Complex(
        \sinh($real) * \cosh($real) / $divisor,
        0.5 * \sin(2 * $imaginary) / $divisor,
        $complex->getSuffix()
    );
}
