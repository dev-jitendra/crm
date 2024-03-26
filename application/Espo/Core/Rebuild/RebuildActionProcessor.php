<?php


namespace Espo\Core\Rebuild;

use Espo\Core\InjectableFactory;
use Espo\Core\Utils\Metadata;

class RebuildActionProcessor
{
    public function __construct(
        private InjectableFactory $injectableFactory,
        private Metadata $metadata
    ) {}

    public function process(): void
    {
        foreach ($this->getActionList() as $action) {
            $action->process();
        }
    }

    
    private function getActionList(): array
    {
        $classNameList = $this->getClassNameList();

        $list = [];

        foreach ($classNameList as $className) {
            $list[] = $this->injectableFactory->create($className);
        }

        return $list;
    }

    
    private function getClassNameList(): array
    {
        
        return $this->metadata->get(['app', 'rebuild', 'actionClassNameList']) ?? [];
    }
}
