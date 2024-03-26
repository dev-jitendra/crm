<?php


namespace Espo\Tools\Import\Api;

use Espo\Core\Acl;
use Espo\Core\Api\Action;
use Espo\Core\Api\Request;
use Espo\Core\Api\Response;
use Espo\Core\Api\ResponseComposer;
use Espo\Core\Exceptions\BadRequest;
use Espo\Core\Exceptions\Forbidden;
use Espo\Entities\Import;
use Espo\Tools\Import\Service;


class PostUnmarkDuplicates implements Action
{
    public function __construct(private Service $service, private Acl $acl) {}

    public function process(Request $request): Response
    {
        if (!$this->acl->checkScope(Import::ENTITY_TYPE)) {
            throw new Forbidden();
        }

        $id = $request->getRouteParam('id');

        if (!$id) {
            throw new BadRequest();
        }

        $data = $request->getParsedBody();

        $entityType = $data->entityType ?? null;
        $entityId = $data->entityId ?? null;

        if (!$entityType || !$entityId) {
            throw new BadRequest("No `entityType` or `entityId`.");
        }

        $this->service->unmarkAsDuplicate($id, $entityType, $entityId);

        return ResponseComposer::json(true);
    }
}
