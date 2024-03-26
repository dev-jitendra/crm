<?php


namespace Espo\Tools\Kanban\Api;

use Espo\Core\Api\Action as ActionAlias;
use Espo\Core\Api\Request;
use Espo\Core\Api\Response;
use Espo\Core\Api\ResponseComposer;
use Espo\Core\Exceptions\BadRequest;
use Espo\Core\Record\SearchParamsFetcher;
use Espo\Tools\Kanban\KanbanService;

class GetData implements ActionAlias
{
    public function __construct(
        private KanbanService $service,
        private SearchParamsFetcher $searchParamsFetcher
    ) {}

    public function process(Request $request): Response
    {
        $entityType = $request->getRouteParam('entityType');

        if (!$entityType) {
            throw new BadRequest();
        }

        $searchParams = $this->searchParamsFetcher->fetch($request);

        $result = $this->service->getData($entityType, $searchParams);

        return ResponseComposer::json([
            'total' => $result->getTotal(),
            'list' => $result->getCollection()->getValueMapList(),
            'additionalData' => $result->getData(),
        ]);
    }
}
