<?php


namespace Espo\Core\Controllers;

use Espo\Core\Exceptions\BadRequest;
use Espo\Core\Api\Request;
use Espo\Core\Exceptions\Error;
use Espo\Core\Exceptions\Forbidden;
use Espo\Core\Exceptions\NotFound;
use Espo\Core\Exceptions\NotFoundSilent;
use Espo\Core\Select\SearchParams;
use Espo\Core\Utils\Json;

use stdClass;

class Record extends RecordBase
{
    
    public function getActionListLinked(Request $request): stdClass
    {
        $id = $request->getRouteParam('id');
        $link = $request->getRouteParam('link');

        if (!$id) {
            throw new BadRequest("No ID.");
        }

        if (!$link) {
            throw new BadRequest("No link.");
        }

        $searchParams = $this->fetchSearchParamsFromRequest($request);

        $recordCollection = $this->getRecordService()->findLinked($id, $link, $searchParams);

        return (object) [
            'total' => $recordCollection->getTotal(),
            'list' => $recordCollection->getValueMapList(),
        ];
    }

    
    public function postActionCreateLink(Request $request): bool
    {
        $id = $request->getRouteParam('id');
        $link = $request->getRouteParam('link');

        $data = $request->getParsedBody();

        if (!$id || !$link) {
            throw new BadRequest();
        }

        if (!empty($data->massRelate)) {
            $searchParams = $this->fetchMassLinkSearchParamsFromRequest($request);

            return $this->getRecordService()->massLink($id, $link, $searchParams);
        }

        $foreignIdList = [];

        if (isset($data->id)) {
            $foreignIdList[] = $data->id;
        }

        if (isset($data->ids) && is_array($data->ids)) {
            foreach ($data->ids as $foreignId) {
                $foreignIdList[] = $foreignId;
            }
        }

        $result = false;

        foreach ($foreignIdList as $foreignId) {
            $this->getRecordService()->link($id, $link, $foreignId);

            $result = true;
        }

        return $result;
    }

    
    public function deleteActionRemoveLink(Request $request): bool
    {
        $id = $request->getRouteParam('id');
        $link = $request->getRouteParam('link');

        $data = $request->getParsedBody();

        if (!$id || !$link) {
            throw new BadRequest();
        }

        $foreignIdList = [];

        if (isset($data->id)) {
            $foreignIdList[] = $data->id;
        }

        if (isset($data->ids) && is_array($data->ids)) {
            foreach ($data->ids as $foreignId) {
                $foreignIdList[] = $foreignId;
            }
        }

        $result = false;

        foreach ($foreignIdList as $foreignId) {
            $this->getRecordService()->unlink($id, $link, $foreignId);

            $result = true;
        }

        return $result;
    }

    
    public function putActionFollow(Request $request): bool
    {
        $id = $request->getRouteParam('id');

        if (!$id) {
            throw new BadRequest("No ID.");
        }

        $this->getRecordService()->follow($id);

        return true;
    }

    
    public function deleteActionUnfollow(Request $request): bool
    {
        $id = $request->getRouteParam('id');

        if (!$id) {
            throw new BadRequest("No ID.");
        }

        $this->getRecordService()->unfollow($id);

        return true;
    }

    
    private function fetchMassLinkSearchParamsFromRequest(Request $request): SearchParams
    {
        $data = $request->getParsedBody();

        $where = $data->where ?? null;

        if ($where !== null) {
            $where = json_decode(Json::encode($where), true);
        }

        $params = json_decode(
            Json::encode(
                $data->searchParams ?? $data->selectData ?? (object) []
            ),
            true
        );

        if ($where !== null && !is_array($where)) {
            throw new BadRequest("Bad 'where.");
        }

        if ($where !== null) {
            $params['where'] = array_merge(
                $params['where'] ?? [],
                $where
            );
        }

        unset($params['select']);

        return SearchParams::fromRaw($params);
    }
}
