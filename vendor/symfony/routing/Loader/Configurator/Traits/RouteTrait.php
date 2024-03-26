<?php



namespace Symfony\Component\Routing\Loader\Configurator\Traits;

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

trait RouteTrait
{
    
    protected $route;

    
    final public function defaults(array $defaults): static
    {
        $this->route->addDefaults($defaults);

        return $this;
    }

    
    final public function requirements(array $requirements): static
    {
        $this->route->addRequirements($requirements);

        return $this;
    }

    
    final public function options(array $options): static
    {
        $this->route->addOptions($options);

        return $this;
    }

    
    final public function utf8(bool $utf8 = true): static
    {
        $this->route->addOptions(['utf8' => $utf8]);

        return $this;
    }

    
    final public function condition(string $condition): static
    {
        $this->route->setCondition($condition);

        return $this;
    }

    
    final public function host(string $pattern): static
    {
        $this->route->setHost($pattern);

        return $this;
    }

    
    final public function schemes(array $schemes): static
    {
        $this->route->setSchemes($schemes);

        return $this;
    }

    
    final public function methods(array $methods): static
    {
        $this->route->setMethods($methods);

        return $this;
    }

    
    final public function controller(callable|string|array $controller): static
    {
        $this->route->addDefaults(['_controller' => $controller]);

        return $this;
    }

    
    final public function locale(string $locale): static
    {
        $this->route->addDefaults(['_locale' => $locale]);

        return $this;
    }

    
    final public function format(string $format): static
    {
        $this->route->addDefaults(['_format' => $format]);

        return $this;
    }

    
    final public function stateless(bool $stateless = true): static
    {
        $this->route->addDefaults(['_stateless' => $stateless]);

        return $this;
    }
}
