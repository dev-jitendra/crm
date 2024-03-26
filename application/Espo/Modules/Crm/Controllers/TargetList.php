<?php


namespace Espo\Modules\Crm\Controllers;

use Espo\Core\Exceptions\BadRequest;
use Espo\Core\Api\Request;
use Espo\Modules\Crm\Services\TargetList as Service;
use Espo\Core\Controllers\Record;

class TargetList extends Record
{
    public function postActionUnlinkAll(Request $request): bool
    {
        $data = $request->getParsedBody();

        if (empty($data->id)) {
            throw new BadRequest();
        }

        if (empty($data->link)) {
            throw new BadRequest();
        }

        $this->getTargetListService()->unlinkAll($data->id, $data->link);

        return true;
    }

    public function postActionOptOut(Request $request): bool
    {
        $data = $request->getParsedBody();

        if (empty($data->id)) {
            throw new BadRequest();
        }

        if (empty($data->targetType)) {
            throw new BadRequest();
        }

        if (empty($data->targetId)) {
            throw new BadRequest();
        }

        $data->id = strval($data->id);
        $data->targetId = strval($data->targetId);

        $this->getTargetListService()->optOut($data->id, $data->targetType, $data->targetId);

        return true;
    }

    public function postActionCancelOptOut(Request $request): bool
    {
        $data = $request->getParsedBody();

        if (empty($data->id)) {
            throw new BadRequest();
        }

        if (empty($data->targetType)) {
            throw new BadRequest();
        }

        if (empty($data->targetId)) {
            throw new BadRequest();
        }

        $data->id = strval($data->id);
        $data->targetId = strval($data->targetId);

        $this->getTargetListService()->cancelOptOut($data->id, $data->targetType, $data->targetId);

        return true;
    }

    private function getTargetListService(): Service
    {
        
        return $this->getRecordService();
    }
}
