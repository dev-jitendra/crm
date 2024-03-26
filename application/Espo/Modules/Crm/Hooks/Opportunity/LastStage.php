<?php


namespace Espo\Modules\Crm\Hooks\Opportunity;

use Espo\Core\Hook\Hook\BeforeSave;
use Espo\Core\Utils\Metadata;
use Espo\Modules\Crm\Entities\Opportunity;
use Espo\ORM\Entity;
use Espo\ORM\Repository\Option\SaveOptions;


class LastStage implements BeforeSave
{
    public static int $order = 8;

    public function __construct(private Metadata $metadata) {}

    
    public function beforeSave(Entity $entity, SaveOptions $options): void
    {
        if (
            $entity->isAttributeChanged('lastStage') ||
            !$entity->isAttributeChanged('stage')
        ) {
            return;
        }

        $probability = $this->metadata
            ->get(['entityDefs', 'Opportunity', 'fields', 'stage', 'probabilityMap', $entity->getStage() ?? '']) ?? 0;

        if ($probability) {
            $entity->set('lastStage', $entity->getStage());

            return;
        }

        $probabilityMap =  $this->metadata
            ->get(['entityDefs', 'Opportunity', 'fields', 'stage', 'probabilityMap']) ?? [];

        $stageList = $this->metadata->get(['entityDefs', 'Opportunity', 'fields', 'stage', 'options']) ?? [];

        if (!count($stageList)) {
            return;
        }

        if ($entity->isNew()) {
            

            $min = 100;
            $minStage = null;

            foreach ($stageList as $stage) {
                $itemProbability = $probabilityMap[$stage] ?? null;

                if (
                    $itemProbability === null ||
                    $itemProbability === 100 ||
                    $itemProbability === 0 ||
                    $itemProbability >= $min
                ) {
                    continue;
                }

                $min = $itemProbability;
                $minStage = $stage;
            }

            if (!$minStage) {
                return;
            }

            $entity->set('lastStage', $minStage);

            return;
        }

        

        if (!$entity->getLastStage()) {
            return;
        }

        $lastStageProbability = $this->metadata
            ->get(['entityDefs', 'Opportunity', 'fields', 'stage', 'probabilityMap', $entity->getLastStage()]) ?? 0;

        if ($lastStageProbability !== 100) {
            return;
        }

        $max = 0;
        $maxStage = null;

        foreach ($stageList as $stage) {
            $itemProbability = $probabilityMap[$stage] ?? null;

            if (
                $itemProbability === null ||
                $itemProbability === 100 ||
                $itemProbability === 0 ||
                $itemProbability <= $max
            ) {
                continue;
            }

            $max = $itemProbability;
            $maxStage = $stage;
        }

        if (!$maxStage) {
            return;
        }

        $entity->set('lastStage', $maxStage);
    }
}
