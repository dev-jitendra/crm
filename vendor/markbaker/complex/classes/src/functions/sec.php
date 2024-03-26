<?php


namespace Complex;


function sec($complex): Complex
{
    $complex = Complex::validateComplexArgument($complex);

    return inverse(cos($complex));
}
