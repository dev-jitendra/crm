<?php

namespace PhpOffice\PhpSpreadsheet\Shared\Trend;

class ExponentialBestFit extends BestFit
{
    
    protected $bestFitType = 'exponential';

    
    public function getValueOfYForX($xValue)
    {
        return $this->getIntersect() * $this->getSlope() ** ($xValue - $this->xOffset);
    }

    
    public function getValueOfXForY($yValue)
    {
        return log(($yValue + $this->yOffset) / $this->getIntersect()) / log($this->getSlope());
    }

    
    public function getEquation($dp = 0)
    {
        $slope = $this->getSlope($dp);
        $intersect = $this->getIntersect($dp);

        return 'Y = ' . $intersect . ' * ' . $slope . '^X';
    }

    
    public function getSlope($dp = 0)
    {
        if ($dp != 0) {
            return round(exp($this->slope), $dp);
        }

        return exp($this->slope);
    }

    
    public function getIntersect($dp = 0)
    {
        if ($dp != 0) {
            return round(exp($this->intersect), $dp);
        }

        return exp($this->intersect);
    }

    
    private function exponentialRegression($yValues, $xValues, $const): void
    {
        foreach ($yValues as &$value) {
            if ($value < 0.0) {
                $value = 0 - log(abs($value));
            } elseif ($value > 0.0) {
                $value = log($value);
            }
        }
        unset($value);

        $this->leastSquareFit($yValues, $xValues, $const);
    }

    
    public function __construct($yValues, $xValues = [], $const = true)
    {
        parent::__construct($yValues, $xValues);

        if (!$this->error) {
            $this->exponentialRegression($yValues, $xValues, $const);
        }
    }
}
