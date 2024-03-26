<?php

namespace PhpOffice\PhpSpreadsheet\Shared\Escher\DggContainer;

class BstoreContainer
{
    
    private $BSECollection = [];

    
    public function addBSE($BSE): void
    {
        $this->BSECollection[] = $BSE;
        $BSE->setParent($this);
    }

    
    public function getBSECollection()
    {
        return $this->BSECollection;
    }
}
