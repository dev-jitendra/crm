<?php


namespace Espo\Tools\Import\Api;

use Espo\Core\Acl;
use Espo\Core\Api\Action;
use Espo\Core\Api\Request;
use Espo\Core\Api\Response;
use Espo\Core\Api\ResponseComposer;
use Espo\Core\Exceptions\Forbidden;
use Espo\Entities\Import;
use Espo\Tools\Import\Service;


class PostFile implements Action
{
    public function __construct(private Service $service, private Acl $acl) {}

    public function process(Request $request): Response
    {
        if (!$this->acl->checkScope(Import::ENTITY_TYPE)) {
            throw new Forbidden();
        }

        $contents = $request->getBodyContents() ?? '';

        $attachmentId = $this->service->uploadFile($contents);

        return ResponseComposer::json(['attachmentId' => $attachmentId]);
    }
}
