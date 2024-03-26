<?php


namespace Espo\Tools\Api\Cors;

use Espo\Core\Utils\Config;
use Psr\Http\Message\RequestInterface as Request;

class DefaultHelper implements Helper
{
    public function __construct(private Config $config) {}

    public function isCredentialsAllowed(Request $request): bool
    {
        return true;
    }

    public function getAllowedOrigin(Request $request): ?string
    {
        $origin = $request->getHeaderLine('Origin');

        if (!$origin) {
            return null;
        }

        return in_array($origin, $this->getAllowedOrigins()) ?
            $origin :
            null;
    }

    public function getAllowedMethods(Request $request): array
    {
        return $this->config->get('apiCorsAllowedMethodList') ?? [];
    }

    public function getAllowedHeaders(Request $request): array
    {
        if (!$request->hasHeader('Access-Control-Request-Headers')) {
            return [];
        }

        return $this->config->get('apiCorsAllowedHeaderList') ?? [];
    }

    public function getSuccessStatus(): ?int
    {
        return null;
    }

    public function getMaxAge(): ?int
    {
        return $this->config->get('apiCorsMaxAge');
    }

    
    private function getAllowedOrigins(): array
    {
        return $this->config->get('apiCorsAllowedOriginList') ?? [];
    }
}
