<?php



namespace Symfony\Component\Routing\Loader;

use Psr\Container\ContainerInterface;


class ContainerLoader extends ObjectLoader
{
    private $container;

    public function __construct(ContainerInterface $container, string $env = null)
    {
        $this->container = $container;
        parent::__construct($env);
    }

    
    public function supports(mixed $resource, string $type = null): bool
    {
        return 'service' === $type && \is_string($resource);
    }

    
    protected function getObject(string $id): object
    {
        return $this->container->get($id);
    }
}
