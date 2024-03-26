<?php



namespace Symfony\Component\HttpClient\Exception;

use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;


final class ClientException extends \RuntimeException implements ClientExceptionInterface
{
    use HttpExceptionTrait;
}
