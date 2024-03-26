<?php


namespace Espo\Tools\Email\Api;

use Espo\Core\Api\Action;
use Espo\Core\Api\Request;
use Espo\Core\Api\Response;
use Espo\Core\Api\ResponseComposer;
use Espo\Core\Exceptions\BadRequest;
use Espo\Tools\Email\InboxService;


class PostInboxRead implements Action
{
    public function __construct(private InboxService $inboxService) {}

    public function process(Request $request): Response
    {
        $data = $request->getParsedBody();

        if (!empty($data->all)) {
            $this->inboxService->markAllAsRead();

            return ResponseComposer::json(true);
        }

        $ids = $data->ids ?? null;
        $id = $data->id ?? null;

        if ($ids === null && is_string($id)) {
            $ids = [$id];
        }

        if (!is_array($ids)) {
            throw new BadRequest("No `ids`.");
        }

        $this->inboxService->markAsReadIdList($ids);

        return ResponseComposer::json(true);
    }
}
