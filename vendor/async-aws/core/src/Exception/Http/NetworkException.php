<?php

declare(strict_types=1);

namespace AsyncAws\Core\Exception\Http;

use AsyncAws\Core\Exception\Exception;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;


class NetworkException extends \RuntimeException implements Exception, TransportExceptionInterface
{
}
