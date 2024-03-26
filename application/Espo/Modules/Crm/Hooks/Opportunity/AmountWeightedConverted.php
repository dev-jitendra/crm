<?php


namespace Espo\Modules\Crm\Hooks\Opportunity;

use Espo\Core\Hook\Hook\AfterSave;
use Espo\Modules\Crm\Entities\Opportunity;
use Espo\ORM\Entity;
use Espo\ORM\Repository\Option\SaveOptions;


class AmountWeightedConverted implements AfterSave
{
    
    public function afterSave(Entity $entity, SaveOptions $options): void
    {
        if (
            !$entity->isAttributeChanged('amount') &&
            !$entity->isAttributeChanged('probability')
        ) {
            return;
        }

        $amountConverted = $entity->get('amountConverted');
        $probability = $entity->get('probability');

        $amountWeightedConverted = round($amountConverted * $probability / 100, 2);

        $entity->set('amountWeightedConverted', $amountWeightedConverted);
    }
}
