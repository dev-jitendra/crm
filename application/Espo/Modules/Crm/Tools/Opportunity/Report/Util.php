<?php


namespace Espo\Modules\Crm\Tools\Opportunity\Report;

use Espo\Core\Utils\Metadata;
use Espo\Modules\Crm\Entities\Opportunity as OpportunityEntity;
use Espo\ORM\EntityManager;
use Espo\ORM\Query\SelectBuilder;

class Util
{
    private Metadata $metadata;
    private EntityManager $entityManager;

    public function __construct(
        Metadata $metadata,
        EntityManager $entityManager
    ) {
        $this->metadata = $metadata;
        $this->entityManager = $entityManager;
    }

    
    public function handleDistinctReportQueryBuilder(SelectBuilder $queryBuilder, array $whereClause): void
    {
        if (!$queryBuilder->build()->isDistinct()) {
            return;
        }

        $subQuery = $this->entityManager
            ->getQueryBuilder()
            ->select()
            ->from(OpportunityEntity::ENTITY_TYPE)
            ->select('id')
            ->where($whereClause)
            ->build();

        $queryBuilder->where([
            'id=s' => $subQuery,
        ]);
    }

    
    public function getLostStageList(): array
    {
        $list = [];

        $probabilityMap =  $this->metadata
            ->get(['entityDefs', OpportunityEntity::ENTITY_TYPE, 'fields', 'stage', 'probabilityMap']) ?? [];

        $stageList = $this->metadata->get('entityDefs.Opportunity.fields.stage.options', []);

        foreach ($stageList as $stage) {
            $value = $probabilityMap[$stage] ?? 0;

            if (!$value) {
                $list[] = $stage;
            }
        }

        return $list;
    }

    
    public function getWonStageList(): array
    {
        $list = [];

        $probabilityMap =  $this->metadata
            ->get(['entityDefs', OpportunityEntity::ENTITY_TYPE, 'fields', 'stage', 'probabilityMap']) ?? [];

        $stageList = $this->metadata->get('entityDefs.Opportunity.fields.stage.options', []);

        foreach ($stageList as $stage) {
            $value = $probabilityMap[$stage] ?? 0;

            if ($value == 100) {
                $list[] = $stage;
            }
        }

        return $list;
    }
}
