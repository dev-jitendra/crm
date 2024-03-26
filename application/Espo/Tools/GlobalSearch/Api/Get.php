<?php


namespace Espo\Tools\GlobalSearch\Api;

use Espo\Core\Api\Action;
use Espo\Core\Api\Request;
use Espo\Core\Api\Response;
use Espo\Core\Api\ResponseComposer;
use Espo\Core\Exceptions\BadRequest;
use Espo\Tools\GlobalSearch\Service;


class Get implements Action
{
    public function __construct(private Service $service)
    {}

    public function process(Request $request): Response
    {
        $query = $request->getQueryParam('q');

        if ($query === null || $query === '') {
            throw new BadRequest("No `q` parameter.");
        }

        $offset = intval($request->getQueryParam('offset'));
        $maxSize = $request->hasQueryParam('maxSize') ?
            intval($request->getQueryParam('maxSize')):
            null;

        $result = $this->service->find($query, $offset, $maxSize);

        return ResponseComposer::json([
            'total' => $result->getTotal(),
            'list' => $result->getValueMapList(),
        ]);
    }
}
