<?php


namespace Espo\Tools\Currency\Api;

use Espo\Core\Api\Action;
use Espo\Core\Api\Request;
use Espo\Core\Api\Response;
use Espo\Core\Api\ResponseComposer;
use Espo\Tools\Currency\RateService as Service;


class Get implements Action
{
    public function __construct(private Service $service)
    {}

    public function process(Request $request): Response
    {
        $result = $this->service->get()->toAssoc();

        return ResponseComposer::json($result);
    }
}
