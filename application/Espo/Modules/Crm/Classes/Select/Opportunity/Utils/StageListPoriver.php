<?php


namespace Espo\Modules\Crm\Classes\Select\Opportunity\Utils;

use Espo\Core\Utils\Metadata;

class StageListPoriver
{
    private $metadata;

    public function __construct(Metadata $metadata)
    {
        $this->metadata = $metadata;
    }

    
    public function getLost(): array
    {
        $lostStageList = [];

        $probabilityMap =  $this->metadata
            ->get(['entityDefs', 'Opportunity', 'fields', 'stage', 'probabilityMap']) ?? [];

        $stageList = $this->metadata->get('entityDefs.Opportunity.fields.stage.options') ?? [];

        foreach ($stageList as $stage) {
            if (empty($probabilityMap[$stage])) {
                $lostStageList[] = $stage;
            }
        }

        return $lostStageList;
    }

    
    public function getWon(): array
    {
        $wonStageList = [];

        $probabilityMap =  $this->metadata
            ->get(['entityDefs', 'Opportunity', 'fields', 'stage', 'probabilityMap']) ?? [];

        $stageList = $this->metadata->get('entityDefs.Opportunity.fields.stage.options') ?? [];

        foreach ($stageList as $stage) {
            if (!empty($probabilityMap[$stage]) && $probabilityMap[$stage] == 100) {
                $wonStageList[] = $stage;
            }
        }

        return $wonStageList;
    }
}
