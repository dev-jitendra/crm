<?php



namespace Symfony\Component\HttpFoundation\Session\Storage\Handler;


class MigratingSessionHandler implements \SessionHandlerInterface, \SessionUpdateTimestampHandlerInterface
{
    
    private \SessionHandlerInterface $currentHandler;

    
    private \SessionHandlerInterface $writeOnlyHandler;

    public function __construct(\SessionHandlerInterface $currentHandler, \SessionHandlerInterface $writeOnlyHandler)
    {
        if (!$currentHandler instanceof \SessionUpdateTimestampHandlerInterface) {
            $currentHandler = new StrictSessionHandler($currentHandler);
        }
        if (!$writeOnlyHandler instanceof \SessionUpdateTimestampHandlerInterface) {
            $writeOnlyHandler = new StrictSessionHandler($writeOnlyHandler);
        }

        $this->currentHandler = $currentHandler;
        $this->writeOnlyHandler = $writeOnlyHandler;
    }

    public function close(): bool
    {
        $result = $this->currentHandler->close();
        $this->writeOnlyHandler->close();

        return $result;
    }

    public function destroy(string $sessionId): bool
    {
        $result = $this->currentHandler->destroy($sessionId);
        $this->writeOnlyHandler->destroy($sessionId);

        return $result;
    }

    public function gc(int $maxlifetime): int|false
    {
        $result = $this->currentHandler->gc($maxlifetime);
        $this->writeOnlyHandler->gc($maxlifetime);

        return $result;
    }

    public function open(string $savePath, string $sessionName): bool
    {
        $result = $this->currentHandler->open($savePath, $sessionName);
        $this->writeOnlyHandler->open($savePath, $sessionName);

        return $result;
    }

    public function read(string $sessionId): string
    {
        
        return $this->currentHandler->read($sessionId);
    }

    public function write(string $sessionId, string $sessionData): bool
    {
        $result = $this->currentHandler->write($sessionId, $sessionData);
        $this->writeOnlyHandler->write($sessionId, $sessionData);

        return $result;
    }

    public function validateId(string $sessionId): bool
    {
        
        return $this->currentHandler->validateId($sessionId);
    }

    public function updateTimestamp(string $sessionId, string $sessionData): bool
    {
        $result = $this->currentHandler->updateTimestamp($sessionId, $sessionData);
        $this->writeOnlyHandler->updateTimestamp($sessionId, $sessionData);

        return $result;
    }
}
