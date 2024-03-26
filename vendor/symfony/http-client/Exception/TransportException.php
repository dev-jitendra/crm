<?php



namespace Symfony\Component\HttpClient\Exception;

use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;


class TransportException extends \RuntimeException implements TransportExceptionInterface
{
}
