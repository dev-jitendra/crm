<?php


namespace Complex;


function acoth($complex): Complex
{
    $complex = Complex::validateComplexArgument($complex);

    return atanh(inverse($complex));
}
