<?php


namespace Espo\Core\ORM\PDO;

use Espo\Core\InjectableFactory;
use Espo\Core\Utils\Metadata;
use Espo\ORM\PDO\PDOFactory;
use RuntimeException;

class PDOFactoryFactory
{
    public function __construct(
        private Metadata $metadata,
        private InjectableFactory $injectableFactory
    ) {}

    public function create(string $platform): PDOFactory
    {
        
        $className =
            $this->metadata->get(['app', 'orm', 'platforms', $platform, 'pdoFactoryClassName']) ??
            $this->metadata->get(['app', 'orm', 'pdoFactoryClassNameMap', $platform]);

        if (!$className) {
            throw new RuntimeException("Could not create PDOFactory.");
        }

        return $this->injectableFactory->create($className);
    }
}
