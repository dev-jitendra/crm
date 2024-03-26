<?php


namespace Espo\Entities;

use Espo\Core\Field\Link;
use Espo\Core\ORM\Entity;

use LogicException;

class ImportError extends Entity
{
    public const ENTITY_TYPE = 'ImportError';

    public const TYPE_VALIDATION = 'Validation';
    public const TYPE_NO_ACCESS = 'No-Access';
    public const TYPE_NOT_FOUND = 'Not-Found';
    public const TYPE_INTEGRITY_CONSTRAINT_VIOLATION = 'Integrity-Constraint-Violation';

    
    public function getType(): ?string
    {
        return $this->get('type');
    }

    public function getExportRowIndex(): int
    {
        return $this->get('exportRowIndex');
    }

    public function getRowIndex(): int
    {
        return $this->get('rowIndex');
    }

    public function getValidationField(): ?string
    {
        return $this->get('validationField');
    }

    public function getValidationType(): ?string
    {
        return $this->get('validationType');
    }

    
    public function getRow(): array
    {
        
        $value = $this->get('row');

        if ($value === null) {
            throw new LogicException();
        }

        return $value;
    }

    public function getImportLink(): Link
    {
        
        $link = $this->getValueObject('import');

        if ($link === null) {
            throw new LogicException();
        }

        return $link;
    }
}
