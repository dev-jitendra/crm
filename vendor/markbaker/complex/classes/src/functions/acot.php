<?php


namespace Complex;


function acot($complex): Complex
{
    $complex = Complex::validateComplexArgument($complex);

    return atan(inverse($complex));
}
