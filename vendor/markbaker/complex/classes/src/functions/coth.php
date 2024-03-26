<?php


namespace Complex;


function coth($complex): Complex
{
    $complex = Complex::validateComplexArgument($complex);
    return inverse(tanh($complex));
}
