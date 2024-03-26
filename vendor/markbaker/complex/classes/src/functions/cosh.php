<?php


namespace Complex;


function cosh($complex): Complex
{
    $complex = Complex::validateComplexArgument($complex);

    if ($complex->isReal()) {
        return new Complex(\cosh($complex->getReal()));
    }

    return new Complex(
        \cosh($complex->getReal()) * \cos($complex->getImaginary()),
        \sinh($complex->getReal()) * \sin($complex->getImaginary()),
        $complex->getSuffix()
    );
}
