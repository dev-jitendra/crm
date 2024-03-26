<?php

namespace PhpOffice\PhpSpreadsheet\Shared\Escher\DggContainer\BstoreContainer\BSE;

class Blip
{
    
    private $parent;

    
    private $data;

    
    public function getData()
    {
        return $this->data;
    }

    
    public function setData($data): void
    {
        $this->data = $data;
    }

    
    public function setParent($parent): void
    {
        $this->parent = $parent;
    }

    
    public function getParent()
    {
        return $this->parent;
    }
}
