<?php


namespace Espo\Tools\UserSecurity\Api;

use Espo\Core\Api\Action;
use Espo\Core\Api\Request;
use Espo\Core\Api\Response;
use Espo\Core\Api\ResponseComposer;
use Espo\Core\Exceptions\BadRequest;
use Espo\Core\Exceptions\Forbidden;
use Espo\Entities\User;
use Espo\Tools\UserSecurity\Password\Service;


class PostPasswordGenerate implements Action
{
    public function __construct(
        private Service $service,
        private User $user
    ) {}

    public function process(Request $request): Response
    {
        if (!$this->user->isAdmin()) {
            throw new Forbidden();
        }

        $id = $request->getParsedBody()->id ?? null;

        if (!$id) {
            throw new BadRequest();
        }

        $this->service->generateAndSendNewPasswordForUser($id);

        return ResponseComposer::json(true);
    }
}
