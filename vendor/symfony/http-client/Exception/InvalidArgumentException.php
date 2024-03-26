<?php



namespace Symfony\Component\HttpClient\Exception;

use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;


final class InvalidArgumentException extends \InvalidArgumentException implements TransportExceptionInterface
{
}
