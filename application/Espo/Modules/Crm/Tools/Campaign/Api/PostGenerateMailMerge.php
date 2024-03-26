<?php


namespace Espo\Modules\Crm\Tools\Campaign\Api;

use Espo\Core\Acl;
use Espo\Core\Acl\Table;
use Espo\Core\Api\Action;
use Espo\Core\Api\Request;
use Espo\Core\Api\Response;
use Espo\Core\Api\ResponseComposer;
use Espo\Core\Exceptions\BadRequest;
use Espo\Core\Exceptions\Forbidden;
use Espo\Modules\Crm\Entities\Campaign;
use Espo\Modules\Crm\Tools\Campaign\MailMergeService;


class PostGenerateMailMerge implements Action
{
    public function __construct(
        private MailMergeService $service,
        private Acl $acl
    ) {}

    public function process(Request $request): Response
    {
        $id = $request->getRouteParam('id');
        $link = $request->getParsedBody()->link ?? null;

        if (!$id) {
            throw new BadRequest();
        }

        if (!$link) {
            throw new BadRequest("No `link`.");
        }

        if (!$this->acl->checkScope(Campaign::ENTITY_TYPE, Table::ACTION_READ)) {
            throw new Forbidden();
        }

        $attachmentId = $this->service->generate($id, $link);

        return ResponseComposer::json(['id' => $attachmentId]);
    }
}
