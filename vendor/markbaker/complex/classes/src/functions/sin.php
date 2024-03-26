<?php


namespace Complex;


function sin($complex): Complex
{
    $complex = Complex::validateComplexArgument($complex);

    if ($complex->isReal()) {
        return new Complex(\sin($complex->getReal()));
    }

    return new Complex(
        \sin($complex->getReal()) * \cosh($complex->getImaginary()),
        \cos($complex->getReal()) * \sinh($complex->getImaginary()),
        $complex->getSuffix()
    );
}
