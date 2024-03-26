<?php


namespace Espo\Core\MassAction\Api;

use Espo\Core\Api\Action;
use Espo\Core\Api\Request;
use Espo\Core\Api\Response;
use Espo\Core\Api\ResponseComposer;
use Espo\Core\Exceptions\BadRequest;
use Espo\Core\MassAction\Service;

class PostSubscribe implements Action
{
    public function __construct(private Service $service)
    {}

    public function process(Request $request): Response
    {
        $id = $request->getRouteParam('id');

        if (!$id) {
            throw new BadRequest();
        }

        $this->service->subscribeToNotificationOnSuccess($id);

        return ResponseComposer::json(true);
    }
}
