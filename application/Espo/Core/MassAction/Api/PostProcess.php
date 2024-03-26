<?php


namespace Espo\Core\MassAction\Api;

use Espo\Core\Api\Action;
use Espo\Core\Api\Request;
use Espo\Core\Api\Response;
use Espo\Core\Api\ResponseComposer;
use Espo\Core\Exceptions\BadRequest;
use Espo\Core\Exceptions\Error;
use Espo\Core\MassAction\Params;
use Espo\Core\MassAction\Service;
use Espo\Core\MassAction\ServiceParams;
use Espo\Core\MassAction\ServiceResult;
use Espo\Core\Utils\Json;
use RuntimeException;
use stdClass;


class PostProcess implements Action
{
    public function __construct(private Service $service)
    {}

    public function process(Request $request): Response
    {
        $body = $request->getParsedBody();

        $entityType = $body->entityType ?? null;
        $action = $body->action ?? null;
        $params = $body->params ?? null;
        $data = $body->data ?? (object) [];
        $isIdle = $body->idle ?? false;

        if (!$entityType || !$action || !$params) {
            throw new BadRequest();
        }

        $rawParams = $this->prepareMassActionParams($params);

        try {
            $massActionParams = Params::fromRaw($rawParams, $entityType);
        }
        catch (RuntimeException $e) {
            throw new BadRequest($e->getMessage());
        }

        $serviceParams = ServiceParams::create($massActionParams)
            ->withIsIdle($isIdle);

        $result = $this->service->process(
            $entityType,
            $action,
            $serviceParams,
            $data
        );

        $result = $this->convertResult($result);

        return ResponseComposer::json($result);
    }

    
    private function prepareMassActionParams(stdClass $data): array
    {
        $where = $data->where ?? null;
        $searchParams = $data->searchParams ?? $data->selectData ?? null;
        $ids = $data->ids ?? null;

        if (!is_null($where) || !is_null($searchParams)) {
            $params = [];

            if (!is_null($where)) {
                $params['where'] = json_decode(Json::encode($where), true);
            }

            if (!is_null($searchParams)) {
                $params['searchParams'] = json_decode(Json::encode($searchParams), true);
            }

            return $params;
        }

        if (is_null($ids)) {
            throw new BadRequest("Bad search params for mass action.");
        }

        return ['ids' => $ids];
    }

    
    private function convertResult(ServiceResult $serviceResult): stdClass
    {
        if (!$serviceResult->hasResult()) {
            return (object) [
                'id' => $serviceResult->getId(),
            ];
        }

        $result = $serviceResult->getResult();

        if (!$result) {
            throw new Error();
        }

        $data = (object) [];

        if ($result->hasCount()) {
            $data->count = $result->getCount();
        }

        if ($result->hasIds()) {
            $data->ids = $result->getIds();
        }

        return $data;
    }
}
