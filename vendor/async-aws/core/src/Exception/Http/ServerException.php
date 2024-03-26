<?php

declare(strict_types=1);

namespace AsyncAws\Core\Exception\Http;

use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;


class ServerException extends \RuntimeException implements HttpException, ServerExceptionInterface
{
    use HttpExceptionTrait;
}
