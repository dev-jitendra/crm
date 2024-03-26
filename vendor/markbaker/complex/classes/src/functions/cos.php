<?php


namespace Complex;


function cos($complex): Complex
{
    $complex = Complex::validateComplexArgument($complex);

    if ($complex->isReal()) {
        return new Complex(\cos($complex->getReal()));
    }

    return conjugate(
        new Complex(
            \cos($complex->getReal()) * \cosh($complex->getImaginary()),
            \sin($complex->getReal()) * \sinh($complex->getImaginary()),
            $complex->getSuffix()
        )
    );
}
