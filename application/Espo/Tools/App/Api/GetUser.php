<?php


namespace Espo\Tools\App\Api;

use Espo\Core\Api\Action;
use Espo\Core\Api\Request;
use Espo\Core\Api\Response;
use Espo\Core\Api\ResponseComposer;
use Espo\Core\InjectableFactory;
use Espo\Tools\App\AppService as Service;


class GetUser implements Action
{
    public function __construct(private InjectableFactory $injectableFactory) {}

    public function process(Request $request): Response
    {
        $data = $this->injectableFactory
            ->create(Service::class)
            ->getUserData();

        return ResponseComposer::json($data);
    }
}
