<?php

namespace PhpOffice\PhpSpreadsheet\Chart;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PlotArea
{
    
    private $layout;

    
    private $plotSeries = [];

    
    public function __construct(?Layout $layout = null, array $plotSeries = [])
    {
        $this->layout = $layout;
        $this->plotSeries = $plotSeries;
    }

    
    public function getLayout()
    {
        return $this->layout;
    }

    
    public function getPlotGroupCount()
    {
        return count($this->plotSeries);
    }

    
    public function getPlotSeriesCount()
    {
        $seriesCount = 0;
        foreach ($this->plotSeries as $plot) {
            $seriesCount += $plot->getPlotSeriesCount();
        }

        return $seriesCount;
    }

    
    public function getPlotGroup()
    {
        return $this->plotSeries;
    }

    
    public function getPlotGroupByIndex($index)
    {
        return $this->plotSeries[$index];
    }

    
    public function setPlotSeries(array $plotSeries)
    {
        $this->plotSeries = $plotSeries;

        return $this;
    }

    public function refresh(Worksheet $worksheet): void
    {
        foreach ($this->plotSeries as $plotSeries) {
            $plotSeries->refresh($worksheet);
        }
    }
}
