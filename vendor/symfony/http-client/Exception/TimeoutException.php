<?php



namespace Symfony\Component\HttpClient\Exception;

use Symfony\Contracts\HttpClient\Exception\TimeoutExceptionInterface;


final class TimeoutException extends TransportException implements TimeoutExceptionInterface
{
}
