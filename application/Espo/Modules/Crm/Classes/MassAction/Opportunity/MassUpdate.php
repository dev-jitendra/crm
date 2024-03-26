<?php


namespace Espo\Modules\Crm\Classes\MassAction\Opportunity;

use Espo\Core\MassAction\Actions\MassUpdate as MassUpdateOriginal;
use Espo\Core\MassAction\Params;
use Espo\Core\MassAction\Result;
use Espo\Core\MassAction\Data;
use Espo\Core\MassAction\MassAction;

use Espo\Tools\MassUpdate\Data as MassUpdateData;

use Espo\Core\Utils\Metadata;

class MassUpdate implements MassAction
{
    private MassUpdateOriginal $massUpdateOriginal;

    private Metadata $metadata;

    public function __construct(MassUpdateOriginal $massUpdateOriginal, Metadata $metadata)
    {
        $this->massUpdateOriginal = $massUpdateOriginal;
        $this->metadata = $metadata;
    }

    public function process(Params $params, Data $data): Result
    {
        $massUpdateData = MassUpdateData::fromMassActionData($data);

        $probability = null;

        $stage = $massUpdateData->getValue('stage');

        if ($stage && !$massUpdateData->has('probability')) {
            $probability = $this->metadata
                ->get(['entityDefs', 'Opportunity', 'fields', 'stage', 'probabilityMap', $stage]);
        }

        if ($probability !== null) {
            $massUpdateData = $massUpdateData->with('probability', $probability);
        }

        return $this->massUpdateOriginal->process($params, $massUpdateData->toMassActionData());
    }
}
