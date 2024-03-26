<?php

namespace PhpOffice\PhpSpreadsheet\Writer;

use PhpOffice\PhpSpreadsheet\Spreadsheet;

interface IWriter
{
    
    public function __construct(Spreadsheet $spreadsheet);

    
    public function getIncludeCharts();

    
    public function setIncludeCharts($pValue);

    
    public function getPreCalculateFormulas();

    
    public function setPreCalculateFormulas($pValue);

    
    public function save($pFilename);

    
    public function getUseDiskCaching();

    
    public function setUseDiskCaching($pValue, $pDirectory = null);

    
    public function getDiskCachingDirectory();
}
