<?php


namespace Espo\Core\MassAction\Actions;

use Espo\Tools\MassUpdate\Processor;
use Espo\Tools\MassUpdate\Data as MassUpdateData;
use Espo\Core\MassAction\Params;
use Espo\Core\MassAction\Result;
use Espo\Core\MassAction\Data;
use Espo\Core\MassAction\MassAction;

class MassUpdate implements MassAction
{
    public function __construct(private Processor $processor)
    {}

    public function process(Params $params, Data $data): Result
    {
        $massUpdateData = MassUpdateData::fromMassActionData($data);

        return $this->processor->process($params, $massUpdateData);
    }
}
