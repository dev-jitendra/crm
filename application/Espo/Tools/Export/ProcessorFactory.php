<?php


namespace Espo\Tools\Export;

use Espo\Core\InjectableFactory;
use Espo\Core\Utils\Metadata;

use LogicException;

class ProcessorFactory
{
    public function __construct(
        private InjectableFactory $injectableFactory,
        private Metadata $metadata
    ) {}

    public function create(string $format): Processor
    {
        if (!in_array($format, $this->metadata->get(['app', 'export', 'formatList']))) {
            throw new LogicException("Not supported export format '{$format}'.");
        }

        
        $className = $this->metadata->get(['app', 'export', 'formatDefs', $format, 'processorClassName']);

        if (!$className) {
            throw new LogicException("No implementation for format '{$format}'.");
        }

        return $this->injectableFactory->create($className);
    }
}
