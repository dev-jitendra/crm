<?php


namespace Espo\Core\EntryPoint;

use Espo\Core\Api\Request;
use Espo\Core\Api\Response;
use Espo\Core\Exceptions\BadRequest;
use Espo\Core\Exceptions\Error;
use Espo\Core\Exceptions\Forbidden;
use Espo\Core\Exceptions\NotFound;


interface EntryPoint
{
    
    public function run(Request $request, Response $response): void;
}
