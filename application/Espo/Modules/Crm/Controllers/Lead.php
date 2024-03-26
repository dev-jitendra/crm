<?php


namespace Espo\Modules\Crm\Controllers;

use Espo\Core\Controllers\Record;
use Espo\Core\Exceptions\BadRequest;
use Espo\Core\Api\Request;
use Espo\Core\Exceptions\ConflictSilent;
use Espo\Core\Exceptions\Forbidden;

use Espo\Modules\Crm\Tools\Lead\Convert\Params as ConvertParams;
use Espo\Modules\Crm\Tools\Lead\Convert\Values;
use Espo\Modules\Crm\Tools\Lead\ConvertService;
use stdClass;

class Lead extends Record
{
    
    public function postActionConvert(Request $request): stdClass
    {
        $data = $request->getParsedBody();

        $id = $data->id ?? null;
        $records = $data->records ?? (object) [];

        if (!$id) {
            throw new BadRequest();
        }

        if (!$records instanceof stdClass) {
            throw new BadRequest();
        }

        $recordsPayload = Values::create();

        foreach (get_object_vars($records) as $entityType => $payload) {
            $recordsPayload = $recordsPayload->with($entityType, $payload);
        }

        $skipDuplicateCheck = $data->skipDuplicateCheck ?? false;

        $params = new ConvertParams($skipDuplicateCheck);

        $lead = $this->injectableFactory
            ->create(ConvertService::class)
            ->convert($id, $recordsPayload, $params);

        return $lead->getValueMap();
    }

    
    public function postActionGetConvertAttributes(Request $request): stdClass
    {
        $data = $request->getParsedBody();

        if (empty($data->id)) {
            throw new BadRequest();
        }

        $data = $this->injectableFactory
            ->create(ConvertService::class)
            ->getValues($data->id);

        return $data->getRaw();
    }
}
