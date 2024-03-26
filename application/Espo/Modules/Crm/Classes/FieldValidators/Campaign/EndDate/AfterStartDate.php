<?php


namespace Espo\Modules\Crm\Classes\FieldValidators\Campaign\EndDate;

use Espo\Core\FieldValidation\Validator;
use Espo\Core\FieldValidation\Validator\Data;
use Espo\Core\FieldValidation\Validator\Failure;

use Espo\Modules\Crm\Entities\Campaign;
use Espo\ORM\Entity;


class AfterStartDate implements Validator
{
    
    public function validate(Entity $entity, string $field, Data $data): ?Failure
    {
        $startDate = $entity->getStartDate();
        $endDate = $entity->getEndDate();

        if (!$startDate || !$endDate) {
            return null;
        }

        if ($endDate->isLessThan($startDate)) {
            return Failure::create();

        }

        return null;
    }
}
