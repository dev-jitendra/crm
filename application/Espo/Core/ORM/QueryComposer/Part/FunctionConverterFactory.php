<?php


namespace Espo\Core\ORM\QueryComposer\Part;

use Espo\ORM\QueryComposer\Part\FunctionConverterFactory as FunctionConverterFactoryInterface;
use Espo\ORM\QueryComposer\Part\FunctionConverter;
use Espo\ORM\DatabaseParams;
use Espo\Core\Utils\Metadata;
use Espo\Core\InjectableFactory;

use LogicException;

class FunctionConverterFactory implements FunctionConverterFactoryInterface
{
    
    private $hash = [];

    public function __construct(
        private Metadata $metadata,
        private InjectableFactory $injectableFactory,
        private DatabaseParams $databaseParams
    ) {}

    public function create(string $name): FunctionConverter
    {
        $className = $this->getClassName($name);

        if ($className === null) {
            throw new LogicException();
        }

        return $this->injectableFactory->create($className);
    }

    public function isCreatable(string $name): bool
    {
        if ($this->getClassName($name) === null) {
            return false;
        }

        return true;
    }

    
    private function getClassName(string $name): ?string
    {
        if (!array_key_exists($name, $this->hash)) {
            
            $platform = $this->databaseParams->getPlatform();

            $this->hash[$name] =
                $this->metadata->get(['app', 'orm', 'platforms', $platform, 'functionConverterClassNameMap', $name]) ??
                $this->metadata->get(['app', 'orm', 'functionConverterClassNameMap_' . $platform, $name]);

        }

        
        return $this->hash[$name];
    }
}
