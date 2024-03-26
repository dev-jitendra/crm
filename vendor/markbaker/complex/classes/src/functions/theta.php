<?php


namespace Complex;


function theta($complex): float
{
    $complex = Complex::validateComplexArgument($complex);

    if ($complex->getReal() == 0.0) {
        if ($complex->isReal()) {
            return 0.0;
        } elseif ($complex->getImaginary() < 0.0) {
            return M_PI / -2;
        }
        return M_PI / 2;
    } elseif ($complex->getReal() > 0.0) {
        return \atan($complex->getImaginary() / $complex->getReal());
    } elseif ($complex->getImaginary() < 0.0) {
        return -(M_PI - \atan(\abs($complex->getImaginary()) / \abs($complex->getReal())));
    }

    return M_PI - \atan($complex->getImaginary() / \abs($complex->getReal()));
}
