<?php


namespace Espo\Tools\LinkManager\Hook;

use Espo\Core\Utils\Metadata;
use Espo\Core\InjectableFactory;
use Espo\Tools\LinkManager\Params;

class HookProcessor
{
    public function __construct(
        private Metadata $metadata,
        private InjectableFactory $injectableFactory
    ) {}

    public function processCreate(Params $params): void
    {
        foreach ($this->getCreateHookList() as $hook) {
            $hook->process($params);
        }
    }

    public function processDelete(Params $params): void
    {
        foreach ($this->getDeleteHookList() as $hook) {
            $hook->process($params);
        }
    }

    
    private function getCreateHookList(): array
    {
        
        $classNameList = $this->metadata->get(['app', 'linkManager', 'createHookClassNameList']) ?? [];

        $list = [];

        foreach ($classNameList as $className) {
            $list[] = $this->injectableFactory->create($className);
        }

        return $list;
    }

    
    private function getDeleteHookList(): array
    {
        
        $classNameList = $this->metadata->get(['app', 'linkManager', 'deleteHookClassNameList']) ?? [];

        $list = [];

        foreach ($classNameList as $className) {
            $list[] = $this->injectableFactory->create($className);
        }

        return $list;
    }
}
