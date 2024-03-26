<?php



namespace Symfony\Component\Routing;


class Route implements \Serializable
{
    private string $path = '/';
    private string $host = '';
    private array $schemes = [];
    private array $methods = [];
    private array $defaults = [];
    private array $requirements = [];
    private array $options = [];
    private string $condition = '';
    private $compiled = null;

    
    public function __construct(string $path, array $defaults = [], array $requirements = [], array $options = [], ?string $host = '', string|array $schemes = [], string|array $methods = [], ?string $condition = '')
    {
        $this->setPath($path);
        $this->addDefaults($defaults);
        $this->addRequirements($requirements);
        $this->setOptions($options);
        $this->setHost($host);
        $this->setSchemes($schemes);
        $this->setMethods($methods);
        $this->setCondition($condition);
    }

    public function __serialize(): array
    {
        return [
            'path' => $this->path,
            'host' => $this->host,
            'defaults' => $this->defaults,
            'requirements' => $this->requirements,
            'options' => $this->options,
            'schemes' => $this->schemes,
            'methods' => $this->methods,
            'condition' => $this->condition,
            'compiled' => $this->compiled,
        ];
    }

    
    final public function serialize(): string
    {
        throw new \BadMethodCallException('Cannot serialize '.__CLASS__);
    }

    public function __unserialize(array $data): void
    {
        $this->path = $data['path'];
        $this->host = $data['host'];
        $this->defaults = $data['defaults'];
        $this->requirements = $data['requirements'];
        $this->options = $data['options'];
        $this->schemes = $data['schemes'];
        $this->methods = $data['methods'];

        if (isset($data['condition'])) {
            $this->condition = $data['condition'];
        }
        if (isset($data['compiled'])) {
            $this->compiled = $data['compiled'];
        }
    }

    
    final public function unserialize(string $serialized)
    {
        $this->__unserialize(unserialize($serialized));
    }

    public function getPath(): string
    {
        return $this->path;
    }

    
    public function setPath(string $pattern): static
    {
        $pattern = $this->extractInlineDefaultsAndRequirements($pattern);

        
        
        $this->path = '/'.ltrim(trim($pattern), '/');
        $this->compiled = null;

        return $this;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    
    public function setHost(?string $pattern): static
    {
        $this->host = $this->extractInlineDefaultsAndRequirements((string) $pattern);
        $this->compiled = null;

        return $this;
    }

    
    public function getSchemes(): array
    {
        return $this->schemes;
    }

    
    public function setSchemes(string|array $schemes): static
    {
        $this->schemes = array_map('strtolower', (array) $schemes);
        $this->compiled = null;

        return $this;
    }

    
    public function hasScheme(string $scheme): bool
    {
        return \in_array(strtolower($scheme), $this->schemes, true);
    }

    
    public function getMethods(): array
    {
        return $this->methods;
    }

    
    public function setMethods(string|array $methods): static
    {
        $this->methods = array_map('strtoupper', (array) $methods);
        $this->compiled = null;

        return $this;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    
    public function setOptions(array $options): static
    {
        $this->options = [
            'compiler_class' => 'Symfony\\Component\\Routing\\RouteCompiler',
        ];

        return $this->addOptions($options);
    }

    
    public function addOptions(array $options): static
    {
        foreach ($options as $name => $option) {
            $this->options[$name] = $option;
        }
        $this->compiled = null;

        return $this;
    }

    
    public function setOption(string $name, mixed $value): static
    {
        $this->options[$name] = $value;
        $this->compiled = null;

        return $this;
    }

    
    public function getOption(string $name): mixed
    {
        return $this->options[$name] ?? null;
    }

    public function hasOption(string $name): bool
    {
        return \array_key_exists($name, $this->options);
    }

    public function getDefaults(): array
    {
        return $this->defaults;
    }

    
    public function setDefaults(array $defaults): static
    {
        $this->defaults = [];

        return $this->addDefaults($defaults);
    }

    
    public function addDefaults(array $defaults): static
    {
        if (isset($defaults['_locale']) && $this->isLocalized()) {
            unset($defaults['_locale']);
        }

        foreach ($defaults as $name => $default) {
            $this->defaults[$name] = $default;
        }
        $this->compiled = null;

        return $this;
    }

    public function getDefault(string $name): mixed
    {
        return $this->defaults[$name] ?? null;
    }

    public function hasDefault(string $name): bool
    {
        return \array_key_exists($name, $this->defaults);
    }

    
    public function setDefault(string $name, mixed $default): static
    {
        if ('_locale' === $name && $this->isLocalized()) {
            return $this;
        }

        $this->defaults[$name] = $default;
        $this->compiled = null;

        return $this;
    }

    public function getRequirements(): array
    {
        return $this->requirements;
    }

    
    public function setRequirements(array $requirements): static
    {
        $this->requirements = [];

        return $this->addRequirements($requirements);
    }

    
    public function addRequirements(array $requirements): static
    {
        if (isset($requirements['_locale']) && $this->isLocalized()) {
            unset($requirements['_locale']);
        }

        foreach ($requirements as $key => $regex) {
            $this->requirements[$key] = $this->sanitizeRequirement($key, $regex);
        }
        $this->compiled = null;

        return $this;
    }

    public function getRequirement(string $key): ?string
    {
        return $this->requirements[$key] ?? null;
    }

    public function hasRequirement(string $key): bool
    {
        return \array_key_exists($key, $this->requirements);
    }

    
    public function setRequirement(string $key, string $regex): static
    {
        if ('_locale' === $key && $this->isLocalized()) {
            return $this;
        }

        $this->requirements[$key] = $this->sanitizeRequirement($key, $regex);
        $this->compiled = null;

        return $this;
    }

    public function getCondition(): string
    {
        return $this->condition;
    }

    
    public function setCondition(?string $condition): static
    {
        $this->condition = (string) $condition;
        $this->compiled = null;

        return $this;
    }

    
    public function compile(): CompiledRoute
    {
        if (null !== $this->compiled) {
            return $this->compiled;
        }

        $class = $this->getOption('compiler_class');

        return $this->compiled = $class::compile($this);
    }

    private function extractInlineDefaultsAndRequirements(string $pattern): string
    {
        if (false === strpbrk($pattern, '?<')) {
            return $pattern;
        }

        return preg_replace_callback('#\{(!?)(\w++)(<.*?>)?(\?[^\}]*+)?\}#', function ($m) {
            if (isset($m[4][0])) {
                $this->setDefault($m[2], '?' !== $m[4] ? substr($m[4], 1) : null);
            }
            if (isset($m[3][0])) {
                $this->setRequirement($m[2], substr($m[3], 1, -1));
            }

            return '{'.$m[1].$m[2].'}';
        }, $pattern);
    }

    private function sanitizeRequirement(string $key, string $regex)
    {
        if ('' !== $regex) {
            if ('^' === $regex[0]) {
                $regex = substr($regex, 1);
            } elseif (0 === strpos($regex, '\\A')) {
                $regex = substr($regex, 2);
            }
        }

        if (str_ends_with($regex, '$')) {
            $regex = substr($regex, 0, -1);
        } elseif (\strlen($regex) - 2 === strpos($regex, '\\z')) {
            $regex = substr($regex, 0, -2);
        }

        if ('' === $regex) {
            throw new \InvalidArgumentException(sprintf('Routing requirement for "%s" cannot be empty.', $key));
        }

        return $regex;
    }

    private function isLocalized(): bool
    {
        return isset($this->defaults['_locale']) && isset($this->defaults['_canonical_route']) && ($this->requirements['_locale'] ?? null) === preg_quote($this->defaults['_locale']);
    }
}
