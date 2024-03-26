<?php

namespace FastRoute;

interface DataGenerator
{
    
    public function addRoute($httpMethod, $routeData, $handler);

    
    public function getData();
}
