<?php



namespace Symfony\Component\Routing\Loader\Configurator;

use Symfony\Component\Routing\RouteCollection;


class ImportConfigurator
{
    use Traits\HostTrait;
    use Traits\PrefixTrait;
    use Traits\RouteTrait;

    private $parent;

    public function __construct(RouteCollection $parent, RouteCollection $route)
    {
        $this->parent = $parent;
        $this->route = $route;
    }

    public function __sleep(): array
    {
        throw new \BadMethodCallException('Cannot serialize '.__CLASS__);
    }

    public function __wakeup()
    {
        throw new \BadMethodCallException('Cannot unserialize '.__CLASS__);
    }

    public function __destruct()
    {
        $this->parent->addCollection($this->route);
    }

    
    final public function prefix(string|array $prefix, bool $trailingSlashOnRoot = true): static
    {
        $this->addPrefix($this->route, $prefix, $trailingSlashOnRoot);

        return $this;
    }

    
    final public function namePrefix(string $namePrefix): static
    {
        $this->route->addNamePrefix($namePrefix);

        return $this;
    }

    
    final public function host(string|array $host): static
    {
        $this->addHost($this->route, $host);

        return $this;
    }
}
