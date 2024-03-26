<?php


namespace Complex;


function acosh($complex)
{
    $complex = Complex::validateComplexArgument($complex);

    if ($complex->isReal() && ($complex->getReal() > 1)) {
        return new Complex(\acosh($complex->getReal()));
    }

    $acosh = acos($complex)
        ->reverse();
    if ($acosh->getReal() < 0.0) {
        $acosh = $acosh->invertReal();
    }

    return $acosh;
}
