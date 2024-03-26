<?php


namespace Complex;


function exp($complex): Complex
{
    $complex = Complex::validateComplexArgument($complex);

    if (($complex->getReal() == 0.0) && (\abs($complex->getImaginary()) == M_PI)) {
        return new Complex(-1.0, 0.0);
    }

    $rho = \exp($complex->getReal());
 
    return new Complex(
        $rho * \cos($complex->getImaginary()),
        $rho * \sin($complex->getImaginary()),
        $complex->getSuffix()
    );
}
