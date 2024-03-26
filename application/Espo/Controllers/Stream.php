<?php


namespace Espo\Controllers;

use Espo\Core\Exceptions\BadRequest;

use Espo\Core\Api\Request;
use Espo\Core\Exceptions\Forbidden;
use Espo\Core\Exceptions\NotFound;
use Espo\Core\Record\SearchParamsFetcher;

use Espo\Core\Select\SearchParams;
use Espo\Core\Select\Where\Item as WhereItem;
use Espo\Entities\User as UserEntity;
use Espo\Tools\Stream\RecordService;

use stdClass;

class Stream
{
    public static string $defaultAction = 'list';

    private RecordService $service;
    private SearchParamsFetcher $searchParamsFetcher;

    public function __construct(
        RecordService $service,
        SearchParamsFetcher $searchParamsFetcher
    ) {
        $this->service = $service;
        $this->searchParamsFetcher = $searchParamsFetcher;
    }

    
    public function getActionList(Request $request): stdClass
    {
        $id = $request->getRouteParam('id');
        $scope = $request->getRouteParam('scope');

        if ($scope === null) {
            throw new BadRequest();
        }

        if ($id === null && $scope !== UserEntity::ENTITY_TYPE) {
            throw new BadRequest("No ID.");
        }

        $searchParams = $this->fetchSearchParams($request);

        $result = $scope === UserEntity::ENTITY_TYPE ?
            $this->service->findUser($id, $searchParams) :
            $this->service->find($scope, $id ?? '', $searchParams);

        return (object) [
            'total' => $result->getTotal(),
            'list' => $result->getValueMapList(),
        ];
    }

    
    public function getActionListPosts(Request $request): stdClass
    {
        $id = $request->getRouteParam('id');
        $scope = $request->getRouteParam('scope');

        if ($scope === null) {
            throw new BadRequest();
        }

        if ($id === null && $scope !== UserEntity::ENTITY_TYPE) {
            throw new BadRequest("No ID.");
        }

        $searchParams = $this->fetchSearchParams($request)
            ->withPrimaryFilter('posts');

        $result = $scope === UserEntity::ENTITY_TYPE ?
            $this->service->findUser($id, $searchParams) :
            $this->service->find($scope, $id ?? '', $searchParams);

        return (object) [
            'total' => $result->getTotal(),
            'list' => $result->getValueMapList(),
        ];
    }

    
    private function fetchSearchParams(Request $request): SearchParams
    {
        $searchParams = $this->searchParamsFetcher->fetch($request);

        $after = $request->getQueryParam('after');
        $filter = $request->getQueryParam('filter');

        if ($after) {
            $searchParams = $searchParams
                ->withWhereAdded(
                    WhereItem
                        ::createBuilder()
                        ->setAttribute('createdAt')
                        ->setType(WhereItem\Type::AFTER)
                        ->setValue($after)
                        ->build()
                );
        }

        if ($filter) {
            $searchParams = $searchParams->withPrimaryFilter($filter);
        }

        if ($request->getQueryParam('skipOwn') === 'true') {
            $searchParams = $searchParams->withBoolFilterAdded('skipOwn');
        }

        return $searchParams;
    }
}
