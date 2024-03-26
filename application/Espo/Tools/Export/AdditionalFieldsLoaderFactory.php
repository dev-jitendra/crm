<?php


namespace Espo\Tools\Export;

use Espo\Core\InjectableFactory;
use Espo\Core\Utils\Metadata;

use LogicException;

class AdditionalFieldsLoaderFactory
{
    public function __construct(
        private InjectableFactory $injectableFactory,
        private Metadata $metadata
    ) {}

    public function create(string $format): AdditionalFieldsLoader
    {
        $className = $this->getClassName($format);

        if (!$className) {
            throw new LogicException();
        }

        return $this->injectableFactory->create($className);
    }

    public function isCreatable(string $format): bool
    {
        return (bool) $this->getClassName($format);
    }

    
    private function getClassName(string $format): ?string
    {
        return $this->metadata->get(['app', 'export', 'formatDefs', $format, 'additionalFieldsLoaderClassName']);
    }
}
