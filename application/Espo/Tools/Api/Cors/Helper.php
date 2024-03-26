<?php


namespace Espo\Tools\Api\Cors;

use Psr\Http\Message\RequestInterface as Request;

interface Helper
{
    public function isCredentialsAllowed(Request $request): bool;

    public function getAllowedOrigin(Request $request): ?string;

    
    public function getAllowedMethods(Request $request): array;

    
    public function getAllowedHeaders(Request $request): array;

    public function getSuccessStatus(): ?int;

    public function getMaxAge(): ?int;
}
