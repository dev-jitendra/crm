<?php


namespace Espo\Core\Portal\Utils;

use Espo\Core\Api\Route as RouteItem;
use Espo\Core\Utils\Route as BaseRoute;

class Route extends BaseRoute
{
    public function getFullList(): array
    {
        $originalRouteList = parent::getFullList();

        $newRouteList = [];

        foreach ($originalRouteList as $route) {
            $path = $route->getAdjustedRoute();

            if ($path[0] !== '/') {
                $path = '/' . $path;
            }

            $path = '/{portalId}' . $path;

            $newRoute = new RouteItem(
                $route->getMethod(),
                $route->getRoute(),
                $path,
                $route->getParams(),
                $route->noAuth(),
                $route->getActionClassName()
            );

            $newRouteList[] = $newRoute;
        }

        return $newRouteList;
    }
}
