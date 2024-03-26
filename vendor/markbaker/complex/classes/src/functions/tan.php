<?php


namespace Complex;


function tan($complex): Complex
{
    $complex = Complex::validateComplexArgument($complex);

    if ($complex->isReal()) {
        return new Complex(\tan($complex->getReal()));
    }

    $real = $complex->getReal();
    $imaginary = $complex->getImaginary();
    $divisor = 1 + \pow(\tan($real), 2) * \pow(\tanh($imaginary), 2);
    if ($divisor == 0.0) {
        throw new \InvalidArgumentException('Division by zero');
    }

    return new Complex(
        \pow(sech($imaginary)->getReal(), 2) * \tan($real) / $divisor,
        \pow(sec($real)->getReal(), 2) * \tanh($imaginary) / $divisor,
        $complex->getSuffix()
    );
}
