<?php


namespace Complex;


function sech($complex): Complex
{
    $complex = Complex::validateComplexArgument($complex);

    return inverse(cosh($complex));
}
