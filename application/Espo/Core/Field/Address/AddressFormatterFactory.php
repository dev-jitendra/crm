<?php


namespace Espo\Core\Field\Address;

use RuntimeException;

use Espo\Core\InjectableFactory;
use Espo\Core\Utils\Config;

class AddressFormatterFactory
{
    public function __construct(
        private AddressFormatterMetadataProvider $metadataProvider,
        private InjectableFactory $injectableFactory,
        private Config $config
    ) {}

    public function create(int $format): AddressFormatter
    {
        
        $className = $this->metadataProvider->getFormatterClassName($format);

        if (!$className) {
            throw new RuntimeException("Unknown address format '{$format}'.");
        }

        return $this->injectableFactory->create($className);
    }

    public function createDefault(): AddressFormatter
    {
        $format = $this->config->get('addressFormat') ?? 1;

        return $this->create($format);
    }
}
