<?php


namespace Espo\Core\WebSocket;

use Espo\Core\InjectableFactory;
use Espo\Core\Utils\Config;
use Espo\Core\Utils\Metadata;
use Espo\Core\Binding\Factory;

use RuntimeException;


class SubscriberFactory implements Factory
{
    private const DEFAULT_MESSAGER = 'ZeroMQ';

    public function __construct(
        private InjectableFactory $injectableFactory,
        private Config $config,
        private Metadata $metadata
    ) {}

    public function create(): Subscriber
    {
        $messager = $this->config->get('webSocketMessager') ?? self::DEFAULT_MESSAGER;

        
        $className = $this->metadata->get(['app', 'webSocket', 'messagers', $messager, 'subscriberClassName']);

        if (!$className) {
            throw new RuntimeException("No subscriber for messager '{$messager}'.");
        }

        return $this->injectableFactory->create($className);
    }
}
