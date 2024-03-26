<?php

namespace PhpOffice\PhpSpreadsheet;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

abstract class DefinedName
{
    protected const REGEXP_IDENTIFY_FORMULA = '[^_\p{N}\p{L}:, \$\'!]';

    
    protected $name;

    
    protected $worksheet;

    
    protected $value;

    
    protected $localOnly;

    
    protected $scope;

    
    protected $isFormula;

    
    public function __construct(
        string $name,
        ?Worksheet $worksheet = null,
        ?string $value = null,
        bool $localOnly = false,
        ?Worksheet $scope = null
    ) {
        if ($worksheet === null) {
            $worksheet = $scope;
        }

        
        $this->name = $name;
        $this->worksheet = $worksheet;
        $this->value = (string) $value;
        $this->localOnly = $localOnly;
        
        $this->scope = ($localOnly === true) ? (($scope === null) ? $worksheet : $scope) : null;
        
        
        
        
        $this->isFormula = self::testIfFormula($this->value);
    }

    
    public static function createInstance(
        string $name,
        ?Worksheet $worksheet = null,
        ?string $value = null,
        bool $localOnly = false,
        ?Worksheet $scope = null
    ): self {
        $value = (string) $value;
        $isFormula = self::testIfFormula($value);
        if ($isFormula) {
            return new NamedFormula($name, $worksheet, $value, $localOnly, $scope);
        }

        return new NamedRange($name, $worksheet, $value, $localOnly, $scope);
    }

    public static function testIfFormula(string $value): bool
    {
        if (substr($value, 0, 1) === '=') {
            $value = substr($value, 1);
        }

        if (is_numeric($value)) {
            return true;
        }

        $segMatcher = false;
        foreach (explode("'", $value) as $subVal) {
            
            if (
                ($segMatcher = !$segMatcher) &&
                (preg_match('/' . self::REGEXP_IDENTIFY_FORMULA . '/miu', $subVal))
            ) {
                return true;
            }
        }

        return false;
    }

    
    public function getName(): string
    {
        return $this->name;
    }

    
    public function setName(string $name): self
    {
        if (!empty($name)) {
            
            $oldTitle = $this->name;

            
            if ($this->worksheet !== null) {
                $this->worksheet->getParent()->removeNamedRange($this->name, $this->worksheet);
            }
            $this->name = $name;

            if ($this->worksheet !== null) {
                $this->worksheet->getParent()->addNamedRange($this);
            }

            
            $newTitle = $this->name;
            ReferenceHelper::getInstance()->updateNamedFormulas($this->worksheet->getParent(), $oldTitle, $newTitle);
        }

        return $this;
    }

    
    public function getWorksheet(): ?Worksheet
    {
        return $this->worksheet;
    }

    
    public function setWorksheet(?Worksheet $value): self
    {
        $this->worksheet = $value;

        return $this;
    }

    
    public function getValue(): string
    {
        return $this->value;
    }

    
    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    
    public function getLocalOnly(): bool
    {
        return $this->localOnly;
    }

    
    public function setLocalOnly(bool $value): self
    {
        $this->localOnly = $value;
        $this->scope = $value ? $this->worksheet : null;

        return $this;
    }

    
    public function getScope(): ?Worksheet
    {
        return $this->scope;
    }

    
    public function setScope(?Worksheet $value): self
    {
        $this->scope = $value;
        $this->localOnly = $value !== null;

        return $this;
    }

    
    public function isFormula(): bool
    {
        return $this->isFormula;
    }

    
    public static function resolveName(string $pDefinedName, Worksheet $pSheet): ?self
    {
        return $pSheet->getParent()->getDefinedName($pDefinedName, $pSheet);
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
