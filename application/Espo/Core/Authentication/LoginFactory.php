<?php


namespace Espo\Core\Authentication;

use Espo\Core\InjectableFactory;
use Espo\Core\Utils\Metadata;

class LoginFactory
{
    public function __construct(
        private InjectableFactory $injectableFactory,
        private Metadata $metadata,
        private ConfigDataProvider $configDataProvider
    ) {}

    public function create(string $method, bool $isPortal = false): Login
    {
        
        $className = $this->metadata->get(['authenticationMethods', $method, 'implementationClassName']);

        if (!$className) {
            $sanitizedName = preg_replace('/[^a-zA-Z0-9]+/', '', $method);

            
            $className = "Espo\\Core\\Authentication\\Logins\\" . $sanitizedName;
        }

        return $this->injectableFactory->createWith($className, [
            'isPortal' => $isPortal,
        ]);
    }

    public function createDefault(): Login
    {
        $method = $this->configDataProvider->getDefaultAuthenticationMethod();

        return $this->create($method);
    }
}
