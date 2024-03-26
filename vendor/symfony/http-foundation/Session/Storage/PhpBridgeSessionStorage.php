<?php



namespace Symfony\Component\HttpFoundation\Session\Storage;

use Symfony\Component\HttpFoundation\Session\Storage\Proxy\AbstractProxy;


class PhpBridgeSessionStorage extends NativeSessionStorage
{
    public function __construct(AbstractProxy|\SessionHandlerInterface $handler = null, MetadataBag $metaBag = null)
    {
        if (!\extension_loaded('session')) {
            throw new \LogicException('PHP extension "session" is required.');
        }

        $this->setMetadataBag($metaBag);
        $this->setSaveHandler($handler);
    }

    
    public function start(): bool
    {
        if ($this->started) {
            return true;
        }

        $this->loadSession();

        return true;
    }

    
    public function clear()
    {
        
        
        foreach ($this->bags as $bag) {
            $bag->clear();
        }

        
        $this->loadSession();
    }
}
