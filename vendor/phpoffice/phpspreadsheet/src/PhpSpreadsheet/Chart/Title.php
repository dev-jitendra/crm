<?php

namespace PhpOffice\PhpSpreadsheet\Chart;

class Title
{
    
    private $caption;

    
    private $layout;

    
    public function __construct($caption = null, ?Layout $layout = null)
    {
        $this->caption = $caption;
        $this->layout = $layout;
    }

    
    public function getCaption()
    {
        return $this->caption;
    }

    
    public function setCaption($caption)
    {
        $this->caption = $caption;

        return $this;
    }

    
    public function getLayout()
    {
        return $this->layout;
    }
}
