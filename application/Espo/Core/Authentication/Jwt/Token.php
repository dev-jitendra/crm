<?php


namespace Espo\Core\Authentication\Jwt;

use Espo\Core\Authentication\Jwt\Token\Header;
use Espo\Core\Authentication\Jwt\Token\Payload;
use RuntimeException;


class Token
{
    private string $token;
    private string $headerPart;
    private string $payloadPart;
    private string $signaturePart;
    private string $headerRaw;
    private string $payloadRaw;
    private string $signatureRaw;
    private Header $header;
    private Payload $payload;

    private function __construct(string $token)
    {
        $this->token = $token;

        $parts = explode('.', $token);

        if (count($parts) < 3) {
            throw new RuntimeException("Too few JWT parts.");
        }

        list($this->headerPart, $this->payloadPart, $this->signaturePart) = $parts;

        $this->headerRaw = Util::base64UrlDecode($this->headerPart);
        $this->payloadRaw = Util::base64UrlDecode($this->payloadPart);
        $this->signatureRaw = Util::base64UrlDecode($this->signaturePart);

        $this->header = Header::fromRaw($this->headerRaw);
        $this->payload = Payload::fromRaw($this->payloadRaw);
    }

    public static function create(string $token): self
    {
        return new self($token);
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getSigningInput(): string
    {
        return $this->headerPart . '.' . $this->payloadPart;
    }

    public function getHeader(): Header
    {
        return $this->header;
    }

    public function getPayload(): Payload
    {
        return $this->payload;
    }

    public function getSignature(): string
    {
        return $this->signatureRaw;
    }

    public function getHeaderRaw(): string
    {
        return $this->headerRaw;
    }

    public function getPayloadRaw(): string
    {
        return $this->payloadRaw;
    }
}
