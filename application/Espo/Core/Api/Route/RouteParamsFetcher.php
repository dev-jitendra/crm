<?php


namespace Espo\Core\Api\Route;

use Espo\Core\Api\Route;

class RouteParamsFetcher
{
    
    public function fetch(Route $item, array $args): array
    {
        $params = [];

        $routeParams = $item->getParams();

        $setKeyList = [];

        foreach (array_keys($routeParams) as $key) {
            $value = $routeParams[$key];

            $paramName = $key;

            if ($value[0] === ':') {
                $realKey = substr($value, 1);

                $params[$paramName] = $args[$realKey];

                $setKeyList[] = $realKey;

                continue;
            }

            $params[$paramName] = $value;
        }

        foreach ($args as $key => $value) {
            if (in_array($key, $setKeyList)) {
                continue;
            }

            $params[$key] = $value;
        }

        return $params;
    }
}
