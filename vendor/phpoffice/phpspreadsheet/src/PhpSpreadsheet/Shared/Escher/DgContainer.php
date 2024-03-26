<?php

namespace PhpOffice\PhpSpreadsheet\Shared\Escher;

class DgContainer
{
    
    private $dgId;

    
    private $lastSpId;

    private $spgrContainer;

    public function getDgId()
    {
        return $this->dgId;
    }

    public function setDgId($value): void
    {
        $this->dgId = $value;
    }

    public function getLastSpId()
    {
        return $this->lastSpId;
    }

    public function setLastSpId($value): void
    {
        $this->lastSpId = $value;
    }

    public function getSpgrContainer()
    {
        return $this->spgrContainer;
    }

    public function setSpgrContainer($spgrContainer)
    {
        return $this->spgrContainer = $spgrContainer;
    }
}
