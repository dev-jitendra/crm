<?php


namespace Complex;


function sqrt($complex): Complex
{
    $complex = Complex::validateComplexArgument($complex);

    $theta = theta($complex);
    $delta1 = \cos($theta / 2);
    $delta2 = \sin($theta / 2);
    $rho = \sqrt(rho($complex));

    return new Complex($delta1 * $rho, $delta2 * $rho, $complex->getSuffix());
}
