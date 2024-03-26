<?php


namespace Espo\Tools\EmailTemplate\Api;

use Espo\Core\Api\Action;
use Espo\Core\Api\Request;
use Espo\Core\Api\Response;
use Espo\Core\Api\ResponseComposer;
use Espo\Core\Exceptions\BadRequest;
use Espo\Tools\EmailTemplate\Data;
use Espo\Tools\EmailTemplate\Service;


class PostPrepare implements Action
{
    public function __construct(private Service $service) {}

    public function process(Request $request): Response
    {
        $id = $request->getRouteParam('id');

        if ($id === null) {
            throw new BadRequest();
        }

        $body = $request->getParsedBody();

        $data = Data::create()
            ->withRelatedType($body->relatedType ?? null)
            ->withRelatedId($body->relatedId ?? null)
            ->withParentType($body->parentType ?? null)
            ->withParentId($body->parentId ?? null)
            ->withEmailAddress($body->emailAddress ?? null);

        $result = $this->service->process($id, $data);

        return ResponseComposer::json($result->getValueMap());
    }
}
