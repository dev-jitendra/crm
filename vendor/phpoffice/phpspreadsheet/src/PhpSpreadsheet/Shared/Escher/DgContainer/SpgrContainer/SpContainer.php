<?php

namespace PhpOffice\PhpSpreadsheet\Shared\Escher\DgContainer\SpgrContainer;

use PhpOffice\PhpSpreadsheet\Shared\Escher\DgContainer\SpgrContainer;

class SpContainer
{
    
    private $parent;

    
    private $spgr = false;

    
    private $spType;

    
    private $spFlag;

    
    private $spId;

    
    private $OPT;

    
    private $startCoordinates;

    
    private $startOffsetX;

    
    private $startOffsetY;

    
    private $endCoordinates;

    
    private $endOffsetX;

    
    private $endOffsetY;

    
    public function setParent($parent): void
    {
        $this->parent = $parent;
    }

    
    public function getParent()
    {
        return $this->parent;
    }

    
    public function setSpgr($value): void
    {
        $this->spgr = $value;
    }

    
    public function getSpgr()
    {
        return $this->spgr;
    }

    
    public function setSpType($value): void
    {
        $this->spType = $value;
    }

    
    public function getSpType()
    {
        return $this->spType;
    }

    
    public function setSpFlag($value): void
    {
        $this->spFlag = $value;
    }

    
    public function getSpFlag()
    {
        return $this->spFlag;
    }

    
    public function setSpId($value): void
    {
        $this->spId = $value;
    }

    
    public function getSpId()
    {
        return $this->spId;
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

    
    public function getOPTCollection()
    {
        return $this->OPT;
    }

    
    public function setStartCoordinates($value): void
    {
        $this->startCoordinates = $value;
    }

    
    public function getStartCoordinates()
    {
        return $this->startCoordinates;
    }

    
    public function setStartOffsetX($startOffsetX): void
    {
        $this->startOffsetX = $startOffsetX;
    }

    
    public function getStartOffsetX()
    {
        return $this->startOffsetX;
    }

    
    public function setStartOffsetY($startOffsetY): void
    {
        $this->startOffsetY = $startOffsetY;
    }

    
    public function getStartOffsetY()
    {
        return $this->startOffsetY;
    }

    
    public function setEndCoordinates($value): void
    {
        $this->endCoordinates = $value;
    }

    
    public function getEndCoordinates()
    {
        return $this->endCoordinates;
    }

    
    public function setEndOffsetX($endOffsetX): void
    {
        $this->endOffsetX = $endOffsetX;
    }

    
    public function getEndOffsetX()
    {
        return $this->endOffsetX;
    }

    
    public function setEndOffsetY($endOffsetY): void
    {
        $this->endOffsetY = $endOffsetY;
    }

    
    public function getEndOffsetY()
    {
        return $this->endOffsetY;
    }

    
    public function getNestingLevel()
    {
        $nestingLevel = 0;

        $parent = $this->getParent();
        while ($parent instanceof SpgrContainer) {
            ++$nestingLevel;
            $parent = $parent->getParent();
        }

        return $nestingLevel;
    }
}
