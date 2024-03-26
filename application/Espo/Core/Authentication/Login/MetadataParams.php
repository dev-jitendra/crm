<?php


namespace Espo\Core\Authentication\Login;


class MetadataParams
{
    private string $method;
    private ?string $credentialsHeader;
    private bool $api;

    public function __construct(
        string $method,
        ?string $credentialsHeader = null,
        bool $api = false
    ) {
        $this->method = $method;
        $this->credentialsHeader = $credentialsHeader;
        $this->api = $api;
    }

    
    public static function fromRaw(string $method, array $data): self
    {
        return new self(
            $method,
            $data['credentialsHeader'] ?? null,
            $data['api'] ?? false,
        );
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getCredentialsHeader(): ?string
    {
        return $this->credentialsHeader;
    }

    public function isApi(): bool
    {
        return $this->api;
    }
}
