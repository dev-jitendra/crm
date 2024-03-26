<?php


namespace Espo\Core\Log;

use Monolog\Handler\HandlerInterface;

use Espo\Core\InjectableFactory;

class HandlerListLoader
{
    public function __construct(
        private readonly InjectableFactory $injectableFactory,
        private readonly DefaultHandlerLoader $defaultLoader
    ) {}

    
    public function load(array $dataList, ?string $defaultLevel = null): array
    {
        $handlerList = [];

        foreach ($dataList as $item) {
            $handler = $this->loadHandler($item, $defaultLevel);

            $handlerList[] = $handler;
        }

        return $handlerList;
    }

    
    private function loadHandler(array $data, ?string $defaultLevel = null): HandlerInterface
    {
        $params = $data['params'] ?? [];
        $params['level'] ??= $defaultLevel;

        
        $loaderClassName = $data['loaderClassName'] ?? null;

        if ($loaderClassName) {
            $loader = $this->injectableFactory->create($loaderClassName);

            return $loader->load($params);
        }

        return $this->defaultLoader->load($data, $defaultLevel);
    }
}
