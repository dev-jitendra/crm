<?php


namespace Espo\Core\Controllers;

use Espo\Core\Exceptions\Forbidden;
use Espo\Core\Exceptions\BadRequest;
use Espo\Core\Exceptions\Error;

use Espo\Services\RecordTree as Service;
use Espo\Core\Api\Request;

use stdClass;

class RecordTree extends Record
{
    
    public static $defaultAction = 'list';

    
    public function getActionListTree(Request $request): stdClass
    {
        if (method_exists($this, 'actionListTree')) {
            
            return (object) $this->actionListTree($request->getRouteParams(), $request->getParsedBody(), $request);
        }

        $where = $request->getQueryParams()['where'] ?? null;
        $parentId = $request->getQueryParam('parentId');
        $maxDepth = $request->getQueryParam('maxDepth');
        $onlyNotEmpty = (bool) $request->getQueryParam('onlyNotEmpty');

        if ($where !== null && !is_array($where)) {
            throw new BadRequest();
        }

        if ($maxDepth !== null) {
            $maxDepth = (int) $maxDepth;
        }

        $collection = $this->getRecordTreeService()->getTree(
            $parentId,
            [
                'where' => $where,
                'onlyNotEmpty' => $onlyNotEmpty,
            ],
            $maxDepth
        );

        if (!$collection) {
            throw new Error();
        }

        return (object) [
            'list' => $collection->getValueMapList(),
            'path' => $this->getRecordTreeService()->getTreeItemPath($parentId),
            'data' => $this->getRecordTreeService()->getCategoryData($parentId),
        ];
    }

    
    public function getActionLastChildrenIdList(Request $request): array
    {
        if (!$this->acl->check($this->name, 'read')) {
            throw new Forbidden();
        }

        $parentId = $request->getQueryParam('parentId');

        return $this->getRecordTreeService()->getLastChildrenIdList($parentId);
    }

    
    protected function getRecordTreeService(): Service
    {
        $service = $this->getRecordService();

        assert($service instanceof Service);

        return $service;
    }
}
