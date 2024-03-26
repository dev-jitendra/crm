<?php


namespace Espo\Core\Authentication\Jwt\Token;

use Espo\Core\Utils\Json;
use RuntimeException;
use JsonException;
use stdClass;


class Payload
{
    private ?string $sub;
    private ?string $iss;
    
    private array $aud;
    private ?int $exp;
    private ?int $iat;
    private ?int $nbf;
    private ?string $nonce;
    private ?int $authTime;
    private ?string $sid;
    
    private array $data;

    
    private function __construct(
        ?string $sub,
        ?string $iss,
        array $aud,
        ?int $exp,
        ?int $iat,
        ?int $nbf,
        ?string $nonce,
        ?int $authTime,
        ?string $sid,
        array $data
    ) {
        $this->sub = $sub;
        $this->iss = $iss;
        $this->aud = $aud;
        $this->exp = $exp;
        $this->iat = $iat;
        $this->nbf = $nbf;
        $this->nonce = $nonce;
        $this->authTime = $authTime;
        $this->sid = $sid;
        $this->data = $data;
    }

    public function getSub(): ?string
    {
        return $this->sub;
    }

    public function getIss(): ?string
    {
        return $this->iss;
    }

    public function getExp(): ?int
    {
        return $this->exp;
    }

    public function getIat(): ?int
    {
        return $this->iat;
    }

    public function getNbf(): ?int
    {
        return $this->nbf;
    }

    
    public function getAud(): array
    {
        return $this->aud;
    }

    public function getNonce(): ?string
    {
        return $this->nonce;
    }

    public function getAuthTime(): ?int
    {
        return $this->authTime;
    }

    public function getSid(): ?string
    {
        return $this->sid;
    }

    
    public function get(string $name)
    {
        return $this->data[$name] ?? null;
    }

    public static function fromRaw(string $raw): self
    {
        $parsed = null;

        try {
            $parsed = Json::decode($raw);
        }
        catch (JsonException $e) {}

        if (!$parsed instanceof stdClass) {
            throw new RuntimeException();
        }

        $sub = $parsed->sub ?? null;
        $iss = $parsed->iss ?? null;
        $aud = $parsed->aud ?? null;
        $exp = $parsed->exp ?? null;
        $iat = $parsed->iat ?? null;
        $nbf = $parsed->nbf ?? null;
        $nonce = $parsed->nonce ?? null;
        $authTime = $parsed->auth_time ?? null;
        $sid = $parsed->sid ?? null;

        if (is_string($aud)) {
            $aud = [$aud];
        }

        if ($aud === null) {
            $aud = [];
        }

        if ($iss !== null && !is_string($sub)) {
            throw new RuntimeException("Bad `sub`.");
        }

        if ($iss !== null && !is_string($iss)) {
            throw new RuntimeException("Bad `iss`.");
        }

        if (!is_array($aud)) {
            throw new RuntimeException("Bad `aud`.");
        }

        if ($exp !== null && !is_numeric($exp)) {
            throw new RuntimeException("Bad `exp`.");
        }

        if ($iat !== null && !is_numeric($iat)) {
            throw new RuntimeException("Bad `iat`.");
        }

        if ($nbf !== null && !is_numeric($nbf)) {
            throw new RuntimeException("Bad `nbf`.");
        }

        if ($nonce !== null && !is_string($nonce)) {
            throw new RuntimeException("Bad `nonce`.");
        }

        if ($authTime !== null && !is_numeric($authTime)) {
            throw new RuntimeException("Bad `auth_time`.");
        }

        if ($sid !== null && !is_string($sid)) {
            throw new RuntimeException("Bad `sid`.");
        }

        if ($exp !== null) {
            $exp = (int) $exp;
        }

        if ($iat !== null) {
            $iat = (int) $iat;
        }

        if ($nbf !== null) {
            $nbf = (int) $nbf;
        }

        if ($authTime !== null) {
            $authTime = (int) $authTime;
        }

        return new self(
            $sub,
            $iss,
            $aud,
            $exp,
            $iat,
            $nbf,
            $nonce,
            $authTime,
            $sid,
            get_object_vars($parsed)
        );
    }
}
