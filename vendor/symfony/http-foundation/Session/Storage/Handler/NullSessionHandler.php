<?php



namespace Symfony\Component\HttpFoundation\Session\Storage\Handler;


class NullSessionHandler extends AbstractSessionHandler
{
    public function close(): bool
    {
        return true;
    }

    public function validateId(string $sessionId): bool
    {
        return true;
    }

    
    protected function doRead(string $sessionId): string
    {
        return '';
    }

    public function updateTimestamp(string $sessionId, string $data): bool
    {
        return true;
    }

    
    protected function doWrite(string $sessionId, string $data): bool
    {
        return true;
    }

    
    protected function doDestroy(string $sessionId): bool
    {
        return true;
    }

    public function gc(int $maxlifetime): int|false
    {
        return 0;
    }
}
