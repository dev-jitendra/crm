<?php

namespace PhpOffice\PhpSpreadsheet\Style;

class Protection extends Supervisor
{
    
    const PROTECTION_INHERIT = 'inherit';
    const PROTECTION_PROTECTED = 'protected';
    const PROTECTION_UNPROTECTED = 'unprotected';

    
    protected $locked;

    
    protected $hidden;

    
    public function __construct($isSupervisor = false, $isConditional = false)
    {
        
        parent::__construct($isSupervisor);

        
        if (!$isConditional) {
            $this->locked = self::PROTECTION_INHERIT;
            $this->hidden = self::PROTECTION_INHERIT;
        }
    }

    
    public function getSharedComponent()
    {
        return $this->parent->getSharedComponent()->getProtection();
    }

    
    public function getStyleArray($array)
    {
        return ['protection' => $array];
    }

    
    public function applyFromArray(array $pStyles)
    {
        if ($this->isSupervisor) {
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($this->getStyleArray($pStyles));
        } else {
            if (isset($pStyles['locked'])) {
                $this->setLocked($pStyles['locked']);
            }
            if (isset($pStyles['hidden'])) {
                $this->setHidden($pStyles['hidden']);
            }
        }

        return $this;
    }

    
    public function getLocked()
    {
        if ($this->isSupervisor) {
            return $this->getSharedComponent()->getLocked();
        }

        return $this->locked;
    }

    
    public function setLocked($pValue)
    {
        if ($this->isSupervisor) {
            $styleArray = $this->getStyleArray(['locked' => $pValue]);
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
        } else {
            $this->locked = $pValue;
        }

        return $this;
    }

    
    public function getHidden()
    {
        if ($this->isSupervisor) {
            return $this->getSharedComponent()->getHidden();
        }

        return $this->hidden;
    }

    
    public function setHidden($pValue)
    {
        if ($this->isSupervisor) {
            $styleArray = $this->getStyleArray(['hidden' => $pValue]);
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
        } else {
            $this->hidden = $pValue;
        }

        return $this;
    }

    
    public function getHashCode()
    {
        if ($this->isSupervisor) {
            return $this->getSharedComponent()->getHashCode();
        }

        return md5(
            $this->locked .
            $this->hidden .
            __CLASS__
        );
    }

    protected function exportArray1(): array
    {
        $exportedArray = [];
        $this->exportArray2($exportedArray, 'locked', $this->getLocked());
        $this->exportArray2($exportedArray, 'hidden', $this->getHidden());

        return $exportedArray;
    }
}
