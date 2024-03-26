<?php


namespace Espo\Tools\Export\Api;

use Espo\Core\Api\Action;
use Espo\Core\Api\Request;
use Espo\Core\Api\Response;
use Espo\Core\Api\ResponseComposer;
use Espo\Core\Exceptions\BadRequest;
use Espo\Core\Utils\Json;
use Espo\Tools\Export\Params;
use Espo\Tools\Export\Service;
use Espo\Tools\Export\ServiceParams;

use stdClass;

class PostProcess implements Action
{
    public function __construct(private Service $service)
    {}

    public function process(Request $request): Response
    {
        $params = $this->fetchRawParamsFromRequest($request);

        $serviceParams = ServiceParams::create()
            ->withIsIdle($request->getParsedBody()->idle ?? false);

        $result = $this->service->process($params, $serviceParams);

        if ($result->hasResult()) {
            $subResult = $result->getResult();

            assert($subResult !== null);

            return ResponseComposer::json([
                'id' => $subResult->getAttachmentId()
            ]);
        }

        return ResponseComposer::json([
            'exportId' => $result->getId()
        ]);
    }

    
    private function fetchRawParamsFromRequest(Request $request): Params
    {
        $data = $request->getParsedBody();

        $entityType = $data->entityType ?? null;

        if (!$entityType) {
            throw new BadRequest("No entityType.");
        }

        $params['entityType'] = $entityType;

        $where = $data->where ?? null;
        $searchParams = $data->searchParams ?? $data->selectData ?? null;
        $ids = $data->ids ?? null;

        if (!is_null($where) || !is_null($searchParams)) {
            if (!is_null($where)) {
                $params['where'] = json_decode(Json::encode($where), true);
            }

            if (!is_null($searchParams)) {
                $params['searchParams'] = json_decode(Json::encode($searchParams), true);
            }
        }
        else if (!is_null($ids)) {
            $params['ids'] = $ids;
        }

        if (isset($data->attributeList)) {
            $params['attributeList'] = $data->attributeList;
        }

        if (isset($data->fieldList)) {
            $params['fieldList'] = $data->fieldList;
        }

        if (isset($data->format)) {
            $params['format'] = $data->format;
        }

        $obj = Params::fromRaw($params);

        if (isset($data->params) && $data->params instanceof stdClass) {
            foreach (get_object_vars($data->params) as $key => $value) {
                $obj = $obj->withParam($key, $value);
            }
        }

        return $obj;
    }
}
