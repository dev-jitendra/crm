<?php


namespace Espo\Tools\Email\Api;

use Espo\Core\Api\Action;
use Espo\Core\Api\Request;
use Espo\Core\Api\Response;
use Espo\Core\Api\ResponseComposer;
use Espo\Tools\Email\InboxService;

class GetNotReadCounts implements Action
{
    public function __construct(private InboxService $inboxService) {}

    public function process(Request $request): Response
    {
        $data = $this->inboxService->getFoldersNotReadCounts();

        return ResponseComposer::json($data);
    }
}
