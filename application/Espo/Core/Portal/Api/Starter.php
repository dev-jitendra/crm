<?php


namespace Espo\Core\Portal\Api;

use Espo\Core\Api\MiddlewareProvider;
use Espo\Core\Api\Starter as StarterBase;
use Espo\Core\ApplicationState;
use Espo\Core\Portal\Utils\Route as RouteUtil;
use Espo\Core\Api\RouteProcessor;
use Espo\Core\Api\Route\RouteParamsFetcher;
use Espo\Core\Utils\Config;
use Espo\Core\Utils\Log;

class Starter extends StarterBase
{
    public function __construct(
        RouteProcessor $requestProcessor,
        RouteUtil $routeUtil,
        RouteParamsFetcher $routeParamsFetcher,
        MiddlewareProvider $middlewareProvider,
        Log $log,
        Config $config,
        ApplicationState $applicationState
    ) {
        $routeCacheFile = 'data/cache/application/slim-routes-portal-' . $applicationState->getPortalId() . '.php';

        parent::__construct(
            $requestProcessor,
            $routeUtil,
            $routeParamsFetcher,
            $middlewareProvider,
            $log,
            $config,
            $routeCacheFile
        );
    }
}
