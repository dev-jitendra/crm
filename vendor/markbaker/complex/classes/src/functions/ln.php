<?php


namespace Complex;


function ln($complex): Complex
{
    $complex = Complex::validateComplexArgument($complex);

    if (($complex->getReal() == 0.0) && ($complex->getImaginary() == 0.0)) {
        throw new \InvalidArgumentException();
    }

    return new Complex(
        \log(rho($complex)),
        theta($complex),
        $complex->getSuffix()
    );
}
