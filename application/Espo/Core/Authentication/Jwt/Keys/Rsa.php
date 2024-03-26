<?php


namespace Espo\Core\Authentication\Jwt\Keys;

use Espo\Core\Authentication\Jwt\Key;
use UnexpectedValueException;
use stdClass;


class Rsa implements Key
{
    private string $kid;
    private string $kty;
    private ?string $alg;
    private string $n;
    private string $e;

    private function __construct(stdClass $raw)
    {
        $kid = $raw->kid ?? null;
        $kty = $raw->kty ?? null;
        $alg = $raw->alg ?? null;
        $n = $raw->n ?? null;
        $e = $raw->e ?? null;

        if ($kid === null || $kty === null) {
            throw new UnexpectedValueException("Bad JWK value.");
        }

        if ($n === null || $e === null) {
            throw new UnexpectedValueException("Bad JWK RSE key. No `n` or `e` values.");
        }

        $this->kid = $kid;
        $this->kty = $kty;
        $this->alg = $alg;
        $this->n = $n;
        $this->e = $e;
    }

    public static function fromRaw(stdClass $raw): self
    {
        return new self($raw);
    }

    public function getKid(): string
    {
        return $this->kid;
    }

    public function getKty(): string
    {
        return $this->kty;
    }

    public function getAlg(): ?string
    {
        return $this->alg;
    }

    public function getN(): string
    {
        return $this->n;
    }

    public function getE(): string
    {
        return $this->e;
    }
}
