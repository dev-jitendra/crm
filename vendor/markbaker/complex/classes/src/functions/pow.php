<?php


namespace Complex;


function pow($complex, $power): Complex
{
    $complex = Complex::validateComplexArgument($complex);

    if (!is_numeric($power)) {
        throw new Exception('Power argument must be a real number');
    }

    if ($complex->getImaginary() == 0.0 && $complex->getReal() >= 0.0) {
        return new Complex(\pow($complex->getReal(), $power));
    }

    $rValue = \sqrt(($complex->getReal() * $complex->getReal()) + ($complex->getImaginary() * $complex->getImaginary()));
    $rPower = \pow($rValue, $power);
    $theta = $complex->argument() * $power;
    if ($theta == 0) {
        return new Complex(1);
    }

    return new Complex($rPower * \cos($theta), $rPower * \sin($theta), $complex->getSuffix());
}
