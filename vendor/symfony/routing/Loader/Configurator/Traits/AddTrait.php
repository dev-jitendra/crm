<?php



namespace Symfony\Component\Routing\Loader\Configurator\Traits;

use Symfony\Component\Routing\Loader\Configurator\AliasConfigurator;
use Symfony\Component\Routing\Loader\Configurator\CollectionConfigurator;
use Symfony\Component\Routing\Loader\Configurator\RouteConfigurator;
use Symfony\Component\Routing\RouteCollection;


trait AddTrait
{
    use LocalizedRouteTrait;

    
    protected $collection;
    protected $name = '';
    protected $prefixes;

    
    public function add(string $name, string|array $path): RouteConfigurator
    {
        $parentConfigurator = $this instanceof CollectionConfigurator ? $this : ($this instanceof RouteConfigurator ? $this->parentConfigurator : null);
        $route = $this->createLocalizedRoute($this->collection, $name, $path, $this->name, $this->prefixes);

        return new RouteConfigurator($this->collection, $route, $this->name, $parentConfigurator, $this->prefixes);
    }

    public function alias(string $name, string $alias): AliasConfigurator
    {
        return new AliasConfigurator($this->collection->addAlias($name, $alias));
    }

    
    public function __invoke(string $name, string|array $path): RouteConfigurator
    {
        return $this->add($name, $path);
    }
}
