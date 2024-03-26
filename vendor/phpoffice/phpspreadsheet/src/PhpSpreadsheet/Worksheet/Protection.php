<?php

namespace PhpOffice\PhpSpreadsheet\Worksheet;

use PhpOffice\PhpSpreadsheet\Shared\PasswordHasher;

class Protection
{
    const ALGORITHM_MD2 = 'MD2';
    const ALGORITHM_MD4 = 'MD4';
    const ALGORITHM_MD5 = 'MD5';
    const ALGORITHM_SHA_1 = 'SHA-1';
    const ALGORITHM_SHA_256 = 'SHA-256';
    const ALGORITHM_SHA_384 = 'SHA-384';
    const ALGORITHM_SHA_512 = 'SHA-512';
    const ALGORITHM_RIPEMD_128 = 'RIPEMD-128';
    const ALGORITHM_RIPEMD_160 = 'RIPEMD-160';
    const ALGORITHM_WHIRLPOOL = 'WHIRLPOOL';

    
    private $sheet = false;

    
    private $objects = false;

    
    private $scenarios = false;

    
    private $formatCells = false;

    
    private $formatColumns = false;

    
    private $formatRows = false;

    
    private $insertColumns = false;

    
    private $insertRows = false;

    
    private $insertHyperlinks = false;

    
    private $deleteColumns = false;

    
    private $deleteRows = false;

    
    private $selectLockedCells = false;

    
    private $sort = false;

    
    private $autoFilter = false;

    
    private $pivotTables = false;

    
    private $selectUnlockedCells = false;

    
    private $password = '';

    
    private $algorithm = '';

    
    private $salt = '';

    
    private $spinCount = 10000;

    
    public function __construct()
    {
    }

    
    public function isProtectionEnabled()
    {
        return $this->sheet ||
            $this->objects ||
            $this->scenarios ||
            $this->formatCells ||
            $this->formatColumns ||
            $this->formatRows ||
            $this->insertColumns ||
            $this->insertRows ||
            $this->insertHyperlinks ||
            $this->deleteColumns ||
            $this->deleteRows ||
            $this->selectLockedCells ||
            $this->sort ||
            $this->autoFilter ||
            $this->pivotTables ||
            $this->selectUnlockedCells;
    }

    
    public function getSheet()
    {
        return $this->sheet;
    }

    
    public function setSheet($pValue)
    {
        $this->sheet = $pValue;

        return $this;
    }

    
    public function getObjects()
    {
        return $this->objects;
    }

    
    public function setObjects($pValue)
    {
        $this->objects = $pValue;

        return $this;
    }

    
    public function getScenarios()
    {
        return $this->scenarios;
    }

    
    public function setScenarios($pValue)
    {
        $this->scenarios = $pValue;

        return $this;
    }

    
    public function getFormatCells()
    {
        return $this->formatCells;
    }

    
    public function setFormatCells($pValue)
    {
        $this->formatCells = $pValue;

        return $this;
    }

    
    public function getFormatColumns()
    {
        return $this->formatColumns;
    }

    
    public function setFormatColumns($pValue)
    {
        $this->formatColumns = $pValue;

        return $this;
    }

    
    public function getFormatRows()
    {
        return $this->formatRows;
    }

    
    public function setFormatRows($pValue)
    {
        $this->formatRows = $pValue;

        return $this;
    }

    
    public function getInsertColumns()
    {
        return $this->insertColumns;
    }

    
    public function setInsertColumns($pValue)
    {
        $this->insertColumns = $pValue;

        return $this;
    }

    
    public function getInsertRows()
    {
        return $this->insertRows;
    }

    
    public function setInsertRows($pValue)
    {
        $this->insertRows = $pValue;

        return $this;
    }

    
    public function getInsertHyperlinks()
    {
        return $this->insertHyperlinks;
    }

    
    public function setInsertHyperlinks($pValue)
    {
        $this->insertHyperlinks = $pValue;

        return $this;
    }

    
    public function getDeleteColumns()
    {
        return $this->deleteColumns;
    }

    
    public function setDeleteColumns($pValue)
    {
        $this->deleteColumns = $pValue;

        return $this;
    }

    
    public function getDeleteRows()
    {
        return $this->deleteRows;
    }

    
    public function setDeleteRows($pValue)
    {
        $this->deleteRows = $pValue;

        return $this;
    }

    
    public function getSelectLockedCells()
    {
        return $this->selectLockedCells;
    }

    
    public function setSelectLockedCells($pValue)
    {
        $this->selectLockedCells = $pValue;

        return $this;
    }

    
    public function getSort()
    {
        return $this->sort;
    }

    
    public function setSort($pValue)
    {
        $this->sort = $pValue;

        return $this;
    }

    
    public function getAutoFilter()
    {
        return $this->autoFilter;
    }

    
    public function setAutoFilter($pValue)
    {
        $this->autoFilter = $pValue;

        return $this;
    }

    
    public function getPivotTables()
    {
        return $this->pivotTables;
    }

    
    public function setPivotTables($pValue)
    {
        $this->pivotTables = $pValue;

        return $this;
    }

    
    public function getSelectUnlockedCells()
    {
        return $this->selectUnlockedCells;
    }

    
    public function setSelectUnlockedCells($pValue)
    {
        $this->selectUnlockedCells = $pValue;

        return $this;
    }

    
    public function getPassword()
    {
        return $this->password;
    }

    
    public function setPassword($pValue, $pAlreadyHashed = false)
    {
        if (!$pAlreadyHashed) {
            $salt = $this->generateSalt();
            $this->setSalt($salt);
            $pValue = PasswordHasher::hashPassword($pValue, $this->getAlgorithm(), $this->getSalt(), $this->getSpinCount());
        }

        $this->password = $pValue;

        return $this;
    }

    
    private function generateSalt(): string
    {
        return base64_encode(random_bytes(16));
    }

    
    public function getAlgorithm(): string
    {
        return $this->algorithm;
    }

    
    public function setAlgorithm(string $algorithm): void
    {
        $this->algorithm = $algorithm;
    }

    
    public function getSalt(): string
    {
        return $this->salt;
    }

    
    public function setSalt(string $salt): void
    {
        $this->salt = $salt;
    }

    
    public function getSpinCount(): int
    {
        return $this->spinCount;
    }

    
    public function setSpinCount(int $spinCount): void
    {
        $this->spinCount = $spinCount;
    }

    
    public function verify(string $password): bool
    {
        if (!$this->isProtectionEnabled()) {
            return true;
        }

        $hash = PasswordHasher::hashPassword($password, $this->getAlgorithm(), $this->getSalt(), $this->getSpinCount());

        return $this->getPassword() === $hash;
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
