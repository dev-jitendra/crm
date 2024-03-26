<?php


namespace Espo\Core\Authentication\Hook;

use Espo\Core\Utils\Metadata;
use Espo\Core\InjectableFactory;

use Espo\Core\Authentication\AuthenticationData;
use Espo\Core\Api\Request;
use Espo\Core\Authentication\Result;

class Manager
{
    private Metadata $metadata;
    private InjectableFactory $injectableFactory;

    public function __construct(Metadata $metadata, InjectableFactory $injectableFactory)
    {
        $this->metadata = $metadata;
        $this->injectableFactory = $injectableFactory;
    }

    public function processBeforeLogin(AuthenticationData $data, Request $request): void
    {
        foreach ($this->getBeforeLoginHookList() as $hook) {
            $hook->process($data, $request);
        }
    }

    public function processOnFail(Result $result, AuthenticationData $data, Request $request): void
    {
        foreach ($this->getOnFailHookList() as $hook) {
            $hook->process($result, $data, $request);
        }
    }

    public function processOnSuccess(Result $result, AuthenticationData $data, Request $request): void
    {
        foreach ($this->getOnSuccessHookList() as $hook) {
            $hook->process($result, $data, $request);
        }
    }

    public function processOnSuccessByToken(Result $result, AuthenticationData $data, Request $request): void
    {
        foreach ($this->getOnSuccessByTokenHookList() as $hook) {
            $hook->process($result, $data, $request);
        }
    }

    public function processOnSecondStepRequired(Result $result, AuthenticationData $data, Request $request): void
    {
        foreach ($this->getOnSecondStepRequiredHookList() as $hook) {
            $hook->process($result, $data, $request);
        }
    }

    
    private function getHookClassNameList(string $type): array
    {
        $key = $type . 'HookClassNameList';

        
        return $this->metadata->get(['app', 'authentication', $key]) ?? [];
    }

    
    private function getBeforeLoginHookList(): array
    {
        $list = [];

        foreach ($this->getHookClassNameList('beforeLogin') as $className) {
            
            $list[] = $this->injectableFactory->create($className);
        }

        return $list;
    }

    
    private function getOnFailHookList(): array
    {
        $list = [];

        foreach ($this->getHookClassNameList('onFail') as $className) {
            
            $list[] = $this->injectableFactory->create($className);
        }

        return $list;
    }

    
    private function getOnSuccessHookList(): array
    {
        $list = [];

        foreach ($this->getHookClassNameList('onSuccess') as $className) {
            
            $list[] = $this->injectableFactory->create($className);
        }

        return $list;
    }

    
    private function getOnSuccessByTokenHookList(): array
    {
        $list = [];

        foreach ($this->getHookClassNameList('onSuccessByToken') as $className) {
            
            $list[] = $this->injectableFactory->create($className);
        }

        return $list;
    }

    
    private function getOnSecondStepRequiredHookList(): array
    {
        $list = [];

        foreach ($this->getHookClassNameList('onSecondStepRequired') as $className) {
            
            $list[] = $this->injectableFactory->create($className);
        }

        return $list;
    }
}
