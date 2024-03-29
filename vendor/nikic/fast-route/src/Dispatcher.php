<?php

namespace FastRoute;

interface Dispatcher
{
    const NOT_FOUND = 0;
    const FOUND = 1;
    const METHOD_NOT_ALLOWED = 2;

    
    public function dispatch($httpMethod, $uri);
}
