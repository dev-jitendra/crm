<?php

declare(strict_types=1);

namespace AsyncAws\Core\Exception\Http;

use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;


final class RedirectionException extends \RuntimeException implements HttpException, RedirectionExceptionInterface
{
    use HttpExceptionTrait;
}
