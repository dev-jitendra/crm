<?php


namespace Complex;


function sinh($complex): Complex
{
    $complex = Complex::validateComplexArgument($complex);

    if ($complex->isReal()) {
        return new Complex(\sinh($complex->getReal()));
    }

    return new Complex(
        \sinh($complex->getReal()) * \cos($complex->getImaginary()),
        \cosh($complex->getReal()) * \sin($complex->getImaginary()),
        $complex->getSuffix()
    );
}
