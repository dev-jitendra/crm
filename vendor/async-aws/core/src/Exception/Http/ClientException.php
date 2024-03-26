<?php

declare(strict_types=1);

namespace AsyncAws\Core\Exception\Http;

use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;


class ClientException extends \RuntimeException implements ClientExceptionInterface, HttpException
{
    use HttpExceptionTrait;
}
