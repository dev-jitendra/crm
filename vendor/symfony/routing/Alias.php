<?php



namespace Symfony\Component\Routing;

use Symfony\Component\Routing\Exception\InvalidArgumentException;

class Alias
{
    private string $id;
    private array $deprecation = [];

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function withId(string $id): static
    {
        $new = clone $this;

        $new->id = $id;

        return $new;
    }

    
    public function getId(): string
    {
        return $this->id;
    }

    
    public function setDeprecated(string $package, string $version, string $message): static
    {
        if ('' !== $message) {
            if (preg_match('#[\r\n]|\*/#', $message)) {
                throw new InvalidArgumentException('Invalid characters found in deprecation template.');
            }

            if (!str_contains($message, '%alias_id%')) {
                throw new InvalidArgumentException('The deprecation template must contain the "%alias_id%" placeholder.');
            }
        }

        $this->deprecation = [
            'package' => $package,
            'version' => $version,
            'message' => $message ?: 'The "%alias_id%" route alias is deprecated. You should stop using it, as it will be removed in the future.',
        ];

        return $this;
    }

    public function isDeprecated(): bool
    {
        return (bool) $this->deprecation;
    }

    
    public function getDeprecation(string $name): array
    {
        return [
            'package' => $this->deprecation['package'],
            'version' => $this->deprecation['version'],
            'message' => str_replace('%alias_id%', $name, $this->deprecation['message']),
        ];
    }
}
