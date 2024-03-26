<?php



namespace Symfony\Contracts\HttpClient\Exception;

use Symfony\Contracts\HttpClient\ResponseInterface;


interface HttpExceptionInterface extends ExceptionInterface
{
    public function getResponse(): ResponseInterface;
}
