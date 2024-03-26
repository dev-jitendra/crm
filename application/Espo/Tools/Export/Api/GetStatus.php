<?php


namespace Espo\Tools\Export\Api;

use Espo\Core\Api\Action;
use Espo\Core\Api\Request;
use Espo\Core\Api\Response;
use Espo\Core\Api\ResponseComposer;
use Espo\Core\Exceptions\BadRequest;
use Espo\Tools\Export\Service;


class GetStatus implements Action
{
    public function __construct(private Service $service)
    {}

    public function process(Request $request): Response
    {
        $id = $request->getRouteParam('id');

        if (!$id) {
            throw new BadRequest();
        }

        $result = $this->service->getStatusData($id);

        return ResponseComposer::json($result);
    }
}
