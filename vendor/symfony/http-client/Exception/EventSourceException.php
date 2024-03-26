<?php



namespace Symfony\Component\HttpClient\Exception;

use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;


final class EventSourceException extends \RuntimeException implements DecodingExceptionInterface
{
}
