<?php


namespace Espo\Tools\Attachment\Api;

use Espo\Core\Api\Action;
use Espo\Core\Api\Request;
use Espo\Core\Api\Response;
use Espo\Core\Api\ResponseComposer;
use Espo\Core\Exceptions\BadRequest;
use Espo\Core\Exceptions\Error;
use Espo\Tools\Attachment\FieldData;
use Espo\Tools\Attachment\Service;


class PostCopy implements Action
{
    public function __construct(private Service $service) {}

    public function process(Request $request): Response
    {
        $id = $request->getRouteParam('id');

        if (!$id) {
            throw new BadRequest();
        }

        $data = $request->getParsedBody();

        $field = $data->field ?? null;
        $parentType = $data->parentType ?? null;
        $relatedType = $data->relatedType ?? null;

        if (!$field) {
            throw new BadRequest("No `field`.");
        }

        try {
            $fieldData = new FieldData($field, $parentType, $relatedType);
        }
        catch (Error $e) {
            throw new BadRequest($e->getMessage());
        }

        $attachment = $this->service->copy($id, $fieldData);

        return ResponseComposer::json($attachment->getValueMap());
    }
}
