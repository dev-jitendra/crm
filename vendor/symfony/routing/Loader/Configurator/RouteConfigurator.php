<?php



namespace Symfony\Component\Routing\Loader\Configurator;

use Symfony\Component\Routing\RouteCollection;


class RouteConfigurator
{
    use Traits\AddTrait;
    use Traits\HostTrait;
    use Traits\RouteTrait;

    protected $parentConfigurator;

    public function __construct(RouteCollection $collection, RouteCollection $route, string $name = '', CollectionConfigurator $parentConfigurator = null, array $prefixes = null)
    {
        $this->collection = $collection;
        $this->route = $route;
        $this->name = $name;
        $this->parentConfigurator = $parentConfigurator; 
        $this->prefixes = $prefixes;
    }

    
    final public function host(string|array $host): static
    {
        $this->addHost($this->route, $host);

        return $this;
    }
}
