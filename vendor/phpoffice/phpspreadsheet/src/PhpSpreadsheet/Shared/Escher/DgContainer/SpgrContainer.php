<?php

namespace PhpOffice\PhpSpreadsheet\Shared\Escher\DgContainer;

class SpgrContainer
{
    
    private $parent;

    
    private $children = [];

    
    public function setParent($parent): void
    {
        $this->parent = $parent;
    }

    
    public function getParent()
    {
        return $this->parent;
    }

    
    public function addChild($child): void
    {
        $this->children[] = $child;
        $child->setParent($this);
    }

    
    public function getChildren()
    {
        return $this->children;
    }

    
    public function getAllSpContainers()
    {
        $allSpContainers = [];

        foreach ($this->children as $child) {
            if ($child instanceof self) {
                $allSpContainers = array_merge($allSpContainers, $child->getAllSpContainers());
            } else {
                $allSpContainers[] = $child;
            }
        }

        return $allSpContainers;
    }
}
