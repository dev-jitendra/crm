<?php


namespace Espo\Tools\UserSecurity\Api;

use Espo\Core\Api\Action;
use Espo\Core\Api\Request;
use Espo\Core\Api\Response;
use Espo\Core\Api\ResponseComposer;
use Espo\Core\Exceptions\BadRequest;
use Espo\Tools\UserSecurity\Password\Service;


class PostChangePasswordByRequest implements Action
{
    public function __construct(private Service $service) {}

    public function process(Request $request): Response
    {
        $data = $request->getParsedBody();

        $requestId = $data->requestId ?? null;
        $password = $data->password ?? null;

        if (!$requestId || $password === null) {
            throw new BadRequest();
        }

        $url = $this->service->changePasswordByRecovery($requestId, $password);

        return ResponseComposer::json(['url' => $url]);
    }
}
