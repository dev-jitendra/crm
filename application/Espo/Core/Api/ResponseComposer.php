<?php


namespace Espo\Core\Api;

use Slim\Psr7\Factory\ResponseFactory;
use Espo\Core\Utils\Json;
use stdClass;

class ResponseComposer
{
    
    public static function json(mixed $data): Response
    {
        return self::empty()
            ->writeBody(Json::encode($data))
            ->setHeader('Content-Type', 'application/json');
    }

    
    public static function empty(): Response
    {
        $psr7Response = (new ResponseFactory())->createResponse();

        return new ResponseWrapper($psr7Response);
    }
}
