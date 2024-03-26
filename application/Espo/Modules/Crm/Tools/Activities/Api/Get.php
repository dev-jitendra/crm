<?php


namespace Espo\Modules\Crm\Tools\Activities\Api;

use Espo\Core\Acl;
use Espo\Core\Api\Action;
use Espo\Core\Api\Request;
use Espo\Core\Api\Response;
use Espo\Core\Api\ResponseComposer;
use Espo\Core\Exceptions\BadRequest;
use Espo\Core\Exceptions\Forbidden;
use Espo\Core\Record\SearchParamsFetcher;
use Espo\Modules\Crm\Tools\Activities\FetchParams as ActivitiesFetchParams;
use Espo\Modules\Crm\Tools\Activities\Service as Service;


class Get implements Action
{
    public function __construct(
        private SearchParamsFetcher $searchParamsFetcher,
        private Service $service,
        private Acl $acl
    ) {}

    public function process(Request $request): Response
    {
        if (!$this->acl->check('Activities')) {
            throw new Forbidden();
        }

        $parentType = $request->getRouteParam('parentType');
        $id = $request->getRouteParam('id');
        $type = $request->getRouteParam('type');

        if (
            !$parentType ||
            !$id ||
            !in_array($type, ['activities', 'history'])
        ) {
            throw new BadRequest();
        }

        $searchParams = $this->searchParamsFetcher->fetch($request);

        $offset = $searchParams->getOffset();
        $maxSize = $searchParams->getMaxSize();

        $targetEntityType = $request->getQueryParam('entityType');

        $fetchParams = new ActivitiesFetchParams($maxSize, $offset, $targetEntityType);

        $recordCollection = $type === 'history' ?
            $this->service->getHistory($parentType, $id, $fetchParams) :
            $this->service->getActivities($parentType, $id, $fetchParams);

        return ResponseComposer::json([
            'total' => $recordCollection->getTotal(),
            'list' => $recordCollection->getValueMapList(),
        ]);
    }
}
