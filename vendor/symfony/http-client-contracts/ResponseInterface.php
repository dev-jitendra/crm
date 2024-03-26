<?php



namespace Symfony\Contracts\HttpClient;

use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;


interface ResponseInterface
{
    
    public function getStatusCode(): int;

    
    public function getHeaders(bool $throw = true): array;

    
    public function getContent(bool $throw = true): string;

    
    public function toArray(bool $throw = true): array;

    
    public function cancel(): void;

    
    public function getInfo(string $type = null);
}
