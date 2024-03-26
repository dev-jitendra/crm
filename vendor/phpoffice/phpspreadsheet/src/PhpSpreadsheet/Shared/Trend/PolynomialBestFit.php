<?php

namespace PhpOffice\PhpSpreadsheet\Shared\Trend;

use PhpOffice\PhpSpreadsheet\Shared\JAMA\Matrix;

class PolynomialBestFit extends BestFit
{
    
    protected $bestFitType = 'polynomial';

    
    protected $order = 0;

    
    public function getOrder()
    {
        return $this->order;
    }

    
    public function getValueOfYForX($xValue)
    {
        $retVal = $this->getIntersect();
        $slope = $this->getSlope();
        foreach ($slope as $key => $value) {
            if ($value != 0.0) {
                $retVal += $value * $xValue ** ($key + 1);
            }
        }

        return $retVal;
    }

    
    public function getValueOfXForY($yValue)
    {
        return ($yValue - $this->getIntersect()) / $this->getSlope();
    }

    
    public function getEquation($dp = 0)
    {
        $slope = $this->getSlope($dp);
        $intersect = $this->getIntersect($dp);

        $equation = 'Y = ' . $intersect;
        foreach ($slope as $key => $value) {
            if ($value != 0.0) {
                $equation .= ' + ' . $value . ' * X';
                if ($key > 0) {
                    $equation .= '^' . ($key + 1);
                }
            }
        }

        return $equation;
    }

    
    public function getSlope($dp = 0)
    {
        if ($dp != 0) {
            $coefficients = [];
            foreach ($this->slope as $coefficient) {
                $coefficients[] = round($coefficient, $dp);
            }

            return $coefficients;
        }

        return $this->slope;
    }

    public function getCoefficients($dp = 0)
    {
        return array_merge([$this->getIntersect($dp)], $this->getSlope($dp));
    }

    
    private function polynomialRegression($order, $yValues, $xValues): void
    {
        
        $x_sum = array_sum($xValues);
        $y_sum = array_sum($yValues);
        $xx_sum = $xy_sum = $yy_sum = 0;
        for ($i = 0; $i < $this->valueCount; ++$i) {
            $xy_sum += $xValues[$i] * $yValues[$i];
            $xx_sum += $xValues[$i] * $xValues[$i];
            $yy_sum += $yValues[$i] * $yValues[$i];
        }
        
        $A = [];
        $B = [];
        for ($i = 0; $i < $this->valueCount; ++$i) {
            for ($j = 0; $j <= $order; ++$j) {
                $A[$i][$j] = $xValues[$i] ** $j;
            }
        }
        for ($i = 0; $i < $this->valueCount; ++$i) {
            $B[$i] = [$yValues[$i]];
        }
        $matrixA = new Matrix($A);
        $matrixB = new Matrix($B);
        $C = $matrixA->solve($matrixB);

        $coefficients = [];
        for ($i = 0; $i < $C->getRowDimension(); ++$i) {
            $r = $C->get($i, 0);
            if (abs($r) <= 10 ** (-9)) {
                $r = 0;
            }
            $coefficients[] = $r;
        }

        $this->intersect = array_shift($coefficients);
        $this->slope = $coefficients;

        $this->calculateGoodnessOfFit($x_sum, $y_sum, $xx_sum, $yy_sum, $xy_sum, 0, 0, 0);
        foreach ($this->xValues as $xKey => $xValue) {
            $this->yBestFitValues[$xKey] = $this->getValueOfYForX($xValue);
        }
    }

    
    public function __construct($order, $yValues, $xValues = [], $const = true)
    {
        parent::__construct($yValues, $xValues);

        if (!$this->error) {
            if ($order < $this->valueCount) {
                $this->bestFitType .= '_' . $order;
                $this->order = $order;
                $this->polynomialRegression($order, $yValues, $xValues);
                if (($this->getGoodnessOfFit() < 0.0) || ($this->getGoodnessOfFit() > 1.0)) {
                    $this->error = true;
                }
            } else {
                $this->error = true;
            }
        }
    }
}
