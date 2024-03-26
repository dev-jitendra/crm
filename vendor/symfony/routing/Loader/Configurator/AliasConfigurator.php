<?php



namespace Symfony\Component\Routing\Loader\Configurator;

use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\Routing\Alias;

class AliasConfigurator
{
    private $alias;

    public function __construct(Alias $alias)
    {
        $this->alias = $alias;
    }

    
    public function deprecate(string $package, string $version, string $message): static
    {
        $this->alias->setDeprecated($package, $version, $message);

        return $this;
    }
}
