<?php

namespace PhpOffice\PhpSpreadsheet\Shared\Escher;

class DggContainer
{
    
    private $spIdMax;

    
    private $cDgSaved;

    
    private $cSpSaved;

    
    private $bstoreContainer;

    
    private $OPT = [];

    
    private $IDCLs = [];

    
    public function getSpIdMax()
    {
        return $this->spIdMax;
    }

    
    public function setSpIdMax($value): void
    {
        $this->spIdMax = $value;
    }

    
    public function getCDgSaved()
    {
        return $this->cDgSaved;
    }

    
    public function setCDgSaved($value): void
    {
        $this->cDgSaved = $value;
    }

    
    public function getCSpSaved()
    {
        return $this->cSpSaved;
    }

    
    public function setCSpSaved($value): void
    {
        $this->cSpSaved = $value;
    }

    
    public function getBstoreContainer()
    {
        return $this->bstoreContainer;
    }

    
    public function setBstoreContainer($bstoreContainer): void
    {
        $this->bstoreContainer = $bstoreContainer;
    }

    
    public function setOPT($property, $value): void
    {
        $this->OPT[$property] = $value;
    }

    
    public function getOPT($property)
    {
        if (isset($this->OPT[$property])) {
            return $this->OPT[$property];
        }

        return null;
    }

    
    public function getIDCLs()
    {
        return $this->IDCLs;
    }

    
    public function setIDCLs($pValue): void
    {
        $this->IDCLs = $pValue;
    }
}
