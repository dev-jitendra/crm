<?php



namespace Symfony\Component\Routing;


class CompiledRoute implements \Serializable
{
    private array $variables;
    private array $tokens;
    private string $staticPrefix;
    private string $regex;
    private array $pathVariables;
    private array $hostVariables;
    private ?string $hostRegex;
    private array $hostTokens;

    
    public function __construct(string $staticPrefix, string $regex, array $tokens, array $pathVariables, string $hostRegex = null, array $hostTokens = [], array $hostVariables = [], array $variables = [])
    {
        $this->staticPrefix = $staticPrefix;
        $this->regex = $regex;
        $this->tokens = $tokens;
        $this->pathVariables = $pathVariables;
        $this->hostRegex = $hostRegex;
        $this->hostTokens = $hostTokens;
        $this->hostVariables = $hostVariables;
        $this->variables = $variables;
    }

    public function __serialize(): array
    {
        return [
            'vars' => $this->variables,
            'path_prefix' => $this->staticPrefix,
            'path_regex' => $this->regex,
            'path_tokens' => $this->tokens,
            'path_vars' => $this->pathVariables,
            'host_regex' => $this->hostRegex,
            'host_tokens' => $this->hostTokens,
            'host_vars' => $this->hostVariables,
        ];
    }

    
    final public function serialize(): string
    {
        throw new \BadMethodCallException('Cannot serialize '.__CLASS__);
    }

    public function __unserialize(array $data): void
    {
        $this->variables = $data['vars'];
        $this->staticPrefix = $data['path_prefix'];
        $this->regex = $data['path_regex'];
        $this->tokens = $data['path_tokens'];
        $this->pathVariables = $data['path_vars'];
        $this->hostRegex = $data['host_regex'];
        $this->hostTokens = $data['host_tokens'];
        $this->hostVariables = $data['host_vars'];
    }

    
    final public function unserialize(string $serialized)
    {
        $this->__unserialize(unserialize($serialized, ['allowed_classes' => false]));
    }

    
    public function getStaticPrefix(): string
    {
        return $this->staticPrefix;
    }

    
    public function getRegex(): string
    {
        return $this->regex;
    }

    
    public function getHostRegex(): ?string
    {
        return $this->hostRegex;
    }

    
    public function getTokens(): array
    {
        return $this->tokens;
    }

    
    public function getHostTokens(): array
    {
        return $this->hostTokens;
    }

    
    public function getVariables(): array
    {
        return $this->variables;
    }

    
    public function getPathVariables(): array
    {
        return $this->pathVariables;
    }

    
    public function getHostVariables(): array
    {
        return $this->hostVariables;
    }
}
