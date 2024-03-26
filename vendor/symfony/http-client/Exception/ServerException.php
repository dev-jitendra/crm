<?php



namespace Symfony\Component\HttpClient\Exception;

use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;


final class ServerException extends \RuntimeException implements ServerExceptionInterface
{
    use HttpExceptionTrait;
}
