<?php


namespace Espo\Core\Loaders;

use Espo\Core\Container\Loader;
use Espo\ORM\Defs;
use Espo\ORM\EntityManager;

class OrmDefs implements Loader
{

    public function __construct(private EntityManager $entityManager)
    {}

    public function load(): Defs
    {
        return $this->entityManager->getMetadata()->getDefs();
    }
}
