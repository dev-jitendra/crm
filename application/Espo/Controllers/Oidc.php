<?php


namespace Espo\Controllers;

use Espo\Core\Api\Request;
use Espo\Core\Api\Response;
use Espo\Core\Exceptions\BadRequest;
use Espo\Core\Exceptions\Error;
use Espo\Core\Exceptions\Forbidden;
use Espo\Core\Exceptions\ForbiddenSilent;
use Espo\Core\Utils\Json;
use Espo\Tools\Oidc\Service;

class Oidc
{
    private Service $service;

    public function __construct(Service $service)
    {
        $this->service = $service;
    }

    
    public function getActionAuthorizationData(Request $request, Response $response): void
    {
        $data = $this->service->getAuthorizationData();

        $response->writeBody(Json::encode($data));
    }


    
    public function postActionBackchannelLogout(Request $request, Response $response): void
    {
        $token = $request->getParsedBody()->logout_token ?? null;

        if (!$token || !is_string($token)) {
            throw new BadRequest();
        }

        $this->service->backchannelLogout($token);

        $response->writeBody('true');
    }
}
