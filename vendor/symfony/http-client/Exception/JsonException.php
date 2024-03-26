<?php



namespace Symfony\Component\HttpClient\Exception;

use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;


final class JsonException extends \JsonException implements DecodingExceptionInterface
{
}
