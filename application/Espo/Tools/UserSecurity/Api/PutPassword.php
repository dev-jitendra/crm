<?php


namespace Espo\Tools\UserSecurity\Api;

use Espo\Core\Api\Action;
use Espo\Core\Api\Request;
use Espo\Core\Api\Response;
use Espo\Core\Api\ResponseComposer;
use Espo\Core\Exceptions\BadRequest;
use Espo\Entities\User;
use Espo\Tools\UserSecurity\Password\Service;


class PutPassword implements Action
{
    public function __construct(
        private Service $service,
        private User $user
    ) {}

    public function process(Request $request): Response
    {
        $data = $request->getParsedBody();

        $password = $data->password ?? null;
        $currentPassword = $data->currentPassword ?? null;

        if (
            !is_string($password) ||
            !is_string($currentPassword)
        ) {
            throw new BadRequest("No `password` or `currentPassword`.");
        }

        $this->service->changePasswordWithCheck($this->user->getId(), $password, $currentPassword);

        return ResponseComposer::json(true);
    }
}
