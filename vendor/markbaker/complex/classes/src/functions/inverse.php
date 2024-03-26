<?php


namespace Complex;


function inverse($complex): Complex
{
    $complex = clone Complex::validateComplexArgument($complex);

    if ($complex->getReal() == 0.0 && $complex->getImaginary() == 0.0) {
        throw new \InvalidArgumentException('Division by zero');
    }

    return $complex->divideInto(1.0);
}
