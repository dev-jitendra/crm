<?php

namespace PhpOffice\PhpSpreadsheet\Shared\Trend;

class LinearBestFit extends BestFit
{
    
    protected $bestFitType = 'linear';

    
    public function getValueOfYForX($xValue)
    {
        return $this->getIntersect() + $this->getSlope() * $xValue;
    }

    
    public function getValueOfXForY($yValue)
    {
        return ($yValue - $this->getIntersect()) / $this->getSlope();
    }

    
    public function getEquation($dp = 0)
    {
        $slope = $this->getSlope($dp);
        $intersect = $this->getIntersect($dp);

        return 'Y = ' . $intersect . ' + ' . $slope . ' * X';
    }

    
    private function linearRegression($yValues, $xValues, $const): void
    {
        $this->leastSquareFit($yValues, $xValues, $const);
    }

    
    public function __construct($yValues, $xValues = [], $const = true)
    {
        parent::__construct($yValues, $xValues);

        if (!$this->error) {
            $this->linearRegression($yValues, $xValues, $const);
        }
    }
}
