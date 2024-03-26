<?php


namespace Espo\Modules\Crm\Hooks\Opportunity;

use Espo\Core\Hook\Hook\BeforeSave;
use Espo\Core\Utils\Metadata;
use Espo\Modules\Crm\Entities\Opportunity;
use Espo\ORM\Entity;
use Espo\ORM\Repository\Option\SaveOptions;


class Probability implements BeforeSave
{
    public static int $order = 7;

    public function __construct(private Metadata $metadata) {}

    
    public function beforeSave(Entity $entity, SaveOptions $options): void
    {
        if (!$entity->isNew()) {
            return;
        }

        if ($entity->has('probability')) {
            return;
        }

        $stage = $entity->getStage();

        if (!$stage) {
            return;
        }

        $probability = $this->metadata
            ->get('entityDefs.Opportunity.fields.stage.probabilityMap.' . $stage) ?? 0;

        if ($probability === null) {
            return;
        }

        $entity->setProbability($probability);
    }
}
