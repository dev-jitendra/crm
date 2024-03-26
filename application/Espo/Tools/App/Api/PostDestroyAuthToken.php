<?php


namespace Espo\Tools\App\Api;

use Espo\Core\Api\Action;
use Espo\Core\Api\Request;
use Espo\Core\Api\Response;
use Espo\Core\Api\ResponseComposer;
use Espo\Core\Authentication\AuthenticationFactory;
use Espo\Core\Exceptions\BadRequest;
use Espo\Core\Exceptions\NotFound;
use Espo\Core\Utils\Json;

class PostDestroyAuthToken implements Action
{
    public function __construct(private AuthenticationFactory $authenticationFactory) {}

    public function process(Request $request): Response
    {
        $data = $request->getParsedBody();

        $token = $data->token ?? null;

        if (!$token || !is_string($token)) {
            throw new BadRequest("No `token`.");
        }

        $authentication = $this->authenticationFactory->create();

        $response = ResponseComposer::empty();

        try {
            $authentication->destroyAuthToken($token, $request, $response);
        }
        catch (NotFound) {
            return $response->writeBody(Json::encode(false));
        }

        return $response->writeBody(Json::encode(true));
    }
}
