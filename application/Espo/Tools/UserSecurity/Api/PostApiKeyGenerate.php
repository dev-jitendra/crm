<?php


namespace Espo\Tools\UserSecurity\Api;

use Espo\Core\Api\Action;
use Espo\Core\Api\Request;
use Espo\Core\Api\Response;
use Espo\Core\Api\ResponseComposer;
use Espo\Core\Exceptions\BadRequest;
use Espo\Core\Exceptions\Forbidden;
use Espo\Entities\User;
use Espo\Tools\UserSecurity\ApiService;


class PostApiKeyGenerate implements Action
{
    public function __construct(
        private ApiService $service,
        private User $user
    ) {}

    public function process(Request $request): Response
    {
        if (!$this->user->isAdmin()) {
            throw new Forbidden();
        }

        $data = $request->getParsedBody();

        $id = $data->id;

        if (!$id) {
            throw new BadRequest();
        }

        $entity = $this->service->generateNewApiKey($id);

        return ResponseComposer::json($entity->getValueMap());
    }
}
