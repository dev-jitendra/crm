<?php

namespace AsyncAws\Core\AwsError;

use Symfony\Contracts\HttpClient\ResponseInterface;

interface AwsErrorFactoryInterface
{
    public function createFromResponse(ResponseInterface $response): AwsError;

    
    public function createFromContent(string $content, array $headers): AwsError;
}
