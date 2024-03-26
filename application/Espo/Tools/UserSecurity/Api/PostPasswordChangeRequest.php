<?php


namespace Espo\Tools\UserSecurity\Api;

use Espo\Core\Api\Action;
use Espo\Core\Api\Request;
use Espo\Core\Api\Response;
use Espo\Core\Api\ResponseComposer;
use Espo\Core\Exceptions\BadRequest;
use Espo\Tools\UserSecurity\Password\RecoveryService;


class PostPasswordChangeRequest implements Action
{
    public function __construct(private RecoveryService $service) {}

    public function process(Request $request): Response
    {
        $data = $request->getParsedBody();

        $userName = $data->userName ?? null;
        $emailAddress = $data->emailAddress ?? null;
        $url = $data->url ?? null;

        if (!$userName || !$emailAddress) {
            throw new BadRequest();
        }

        if (!is_string($userName) || !is_string($emailAddress)) {
            throw new BadRequest();
        }

        $this->service->request($emailAddress, $userName, $url);

        return ResponseComposer::json(true);
    }
}
