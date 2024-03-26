<?php


namespace Espo\Core\Authentication\Jwt\Token;

use Espo\Core\Utils\Json;
use RuntimeException;
use JsonException;
use stdClass;


class Header
{
    private string $alg;
    private ?string $kid;
    
    private array $data;

    
    private function __construct(
        string $alg,
        ?string $kid,
        array $data
    ) {
        $this->alg = $alg;
        $this->kid = $kid;
        $this->data = $data;
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

        $alg = self::obtainFromParsedString($parsed, 'alg');
        $kid = self::obtainFromParsedStringNull($parsed, 'kid');

        return new self(
            $alg,
            $kid,
            get_object_vars($parsed)
        );
    }

    private static function obtainFromParsedString(stdClass $parsed, string $name): string
    {
        $value = $parsed->$name ?? null;

        if (!is_string($value)) {
            throw new RuntimeException("No or bad `{$name}` in JWT header.");
        }

        return $value;
    }

    private static function obtainFromParsedStringNull(stdClass $parsed, string $name): ?string
    {
        $value = $parsed->$name ?? null;

        if ($value !== null && !is_string($value)) {
            throw new RuntimeException("Bad `{$name}` in JWT header.");
        }

        return $value;
    }

    public function getAlg(): string
    {
        return $this->alg;
    }

    public function getKid(): ?string
    {
        return $this->kid;
    }
}
