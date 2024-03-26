<?php


namespace Espo\Core\Sms;

use Espo\Core\Binding\Factory;
use Espo\Core\Utils\Config;
use Espo\Core\Utils\Metadata;
use Espo\Core\InjectableFactory;

use RuntimeException;


class SenderFactory implements Factory
{
    public function __construct(
        private Config $config,
        private Metadata $metadata,
        private InjectableFactory $injectableFactory
    ) {}

    public function create(): Sender
    {
        $provider = $this->config->get('smsProvider');

        if (!$provider) {
            throw new RuntimeException("No `smsProvider` in config.");
        }

        
        $className = $this->metadata->get(['app', 'smsProviders', $provider, 'senderClassName']);

        if (!$className) {
            throw new RuntimeException("No `senderClassName` for '$provider' provider.");
        }

        return $this->injectableFactory->create($className);
    }
}
