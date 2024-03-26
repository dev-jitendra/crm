<?php

namespace PhpOffice\PhpSpreadsheet\Document;

use PhpOffice\PhpSpreadsheet\Shared\PasswordHasher;

class Security
{
    
    private $lockRevision = false;

    
    private $lockStructure = false;

    
    private $lockWindows = false;

    
    private $revisionsPassword = '';

    
    private $workbookPassword = '';

    
    public function __construct()
    {
    }

    
    public function isSecurityEnabled()
    {
        return  $this->lockRevision ||
                $this->lockStructure ||
                $this->lockWindows;
    }

    
    public function getLockRevision()
    {
        return $this->lockRevision;
    }

    
    public function setLockRevision($pValue)
    {
        $this->lockRevision = $pValue;

        return $this;
    }

    
    public function getLockStructure()
    {
        return $this->lockStructure;
    }

    
    public function setLockStructure($pValue)
    {
        $this->lockStructure = $pValue;

        return $this;
    }

    
    public function getLockWindows()
    {
        return $this->lockWindows;
    }

    
    public function setLockWindows($pValue)
    {
        $this->lockWindows = $pValue;

        return $this;
    }

    
    public function getRevisionsPassword()
    {
        return $this->revisionsPassword;
    }

    
    public function setRevisionsPassword($pValue, $pAlreadyHashed = false)
    {
        if (!$pAlreadyHashed) {
            $pValue = PasswordHasher::hashPassword($pValue);
        }
        $this->revisionsPassword = $pValue;

        return $this;
    }

    
    public function getWorkbookPassword()
    {
        return $this->workbookPassword;
    }

    
    public function setWorkbookPassword($pValue, $pAlreadyHashed = false)
    {
        if (!$pAlreadyHashed) {
            $pValue = PasswordHasher::hashPassword($pValue);
        }
        $this->workbookPassword = $pValue;

        return $this;
    }

    
    public function __clone()
    {
        $vars = get_object_vars($this);
        foreach ($vars as $key => $value) {
            if (is_object($value)) {
                $this->$key = clone $value;
            } else {
                $this->$key = $value;
            }
        }
    }
}
