<?php



namespace Symfony\Component\Routing\Loader;

use Symfony\Component\Config\Loader\FileLoader;
use Symfony\Component\Routing\RouteCollection;


class GlobFileLoader extends FileLoader
{
    
    public function load(mixed $resource, string $type = null): mixed
    {
        $collection = new RouteCollection();

        foreach ($this->glob($resource, false, $globResource) as $path => $info) {
            $collection->addCollection($this->import($path));
        }

        $collection->addResource($globResource);

        return $collection;
    }

    
    public function supports(mixed $resource, string $type = null): bool
    {
        return 'glob' === $type;
    }
}
