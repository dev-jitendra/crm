<?php


namespace Espo\Core\Log;

use Monolog\Handler\HandlerInterface;

interface HandlerLoader
{
    
    public function load(array $params): HandlerInterface;
}
