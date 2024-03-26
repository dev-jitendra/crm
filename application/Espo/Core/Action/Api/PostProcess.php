<?php


namespace Espo\Core\Action\Api;

use Espo\Core\Api\Action;
use Espo\Core\Api\Request;
use Espo\Core\Api\Response;
use Espo\Core\Action\Service;
use Espo\Core\Api\ResponseComposer;
use Espo\Core\Exceptions\BadRequest;


class PostProcess implements Action
{
    public function __construct(private Service $service)
    {}

    public function process(Request $request): Response
    {
        $body = $request->getParsedBody();

        $entityType = $body->entityType ?? null;
        $id = $body->id ?? null;
        $action = $body->action ?? null;
        $data = $body->data ?? (object) [];

        if (!$entityType || !$action || !$id) {
            throw new BadRequest();
        }

        $entity = $this->service->process($entityType, $action, $id, $data);

        return ResponseComposer::json($entity->getValueMap());
    }
}
