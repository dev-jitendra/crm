<?php


namespace Espo\Tools\Attachment\Api;

use Espo\Core\Api\Action;
use Espo\Core\Api\Request;
use Espo\Core\Api\Response;
use Espo\Core\Api\ResponseComposer;
use Espo\Core\Exceptions\BadRequest;
use Espo\Tools\Attachment\UploadService;


class PostChunk implements Action
{
    public function __construct(private UploadService $uploadService) {}

    public function process(Request $request): Response
    {
        $id = $request->getRouteParam('id');
        $body = $request->getBodyContents();

        if (!$id || !$body) {
            throw new BadRequest();
        }

        $this->uploadService->uploadChunk($id, $body);

        return ResponseComposer::json(true);
    }
}
