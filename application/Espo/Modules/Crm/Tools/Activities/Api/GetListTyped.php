<?php


namespace Espo\Modules\Crm\Tools\Activities\Api;

use Espo\Core\Api\Action;
use Espo\Core\Api\Request;
use Espo\Core\Api\Response;
use Espo\Core\Api\ResponseComposer;
use Espo\Core\Exceptions\BadRequest;
use Espo\Core\Record\SearchParamsFetcher;
use Espo\Modules\Crm\Tools\Activities\Service as Service;


class GetListTyped implements Action
{
    public function __construct(
        private SearchParamsFetcher $searchParamsFetcher,
        private Service $service
    ) {}

    public function process(Request $request): Response
    {
        $parentType = $request->getRouteParam('parentType');
        $id = $request->getRouteParam('id');
        $type = $request->getRouteParam('type');
        $targetType = $request->getRouteParam('targetType');

        if (
            !$parentType ||
            !$id ||
            !$type ||
            !$targetType
        ) {
            throw new BadRequest();
        }

        if ($type === 'activities') {
            $isHistory = false;
        }
        else  if ($type === 'history') {
            $isHistory = true;
        }
        else {
            throw new BadRequest("Bad type.");
        }

        $searchParams = $this->searchParamsFetcher->fetch($request);

        $result = $this->service->findActivitiesEntityType(
            $parentType,
            $id,
            $targetType,
            $isHistory,
            $searchParams
        );

        return ResponseComposer::json([
            'total' => $result->getTotal(),
            'list' => $result->getValueMapList(),
        ]);
    }
}
