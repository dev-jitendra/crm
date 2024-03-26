<?php


namespace Espo\Tools\Currency\Api;

use Espo\Core\Api\Action;
use Espo\Core\Api\Request;
use Espo\Core\Api\Response;
use Espo\Core\Api\ResponseComposer;
use Espo\Core\Currency\Rates;
use Espo\Tools\Currency\RateService as Service;


class PutUpdate implements Action
{
    public function __construct(private Service $service)
    {}

    public function process(Request $request): Response
    {
        $data = $request->getParsedBody();

        $rates = Rates::fromAssoc(get_object_vars($data), '___');

        $this->service->set($rates);

        return ResponseComposer::json(true);
    }
}
