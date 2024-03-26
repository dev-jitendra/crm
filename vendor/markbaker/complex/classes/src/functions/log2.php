<?php


namespace Complex;


function log2($complex): Complex
{
    $complex = Complex::validateComplexArgument($complex);

    if (($complex->getReal() == 0.0) && ($complex->getImaginary() == 0.0)) {
        throw new \InvalidArgumentException();
    } elseif (($complex->getReal() > 0.0) && ($complex->getImaginary() == 0.0)) {
        return new Complex(\log($complex->getReal(), 2), 0.0, $complex->getSuffix());
    }

    return ln($complex)
        ->multiply(\log(Complex::EULER, 2));
}
