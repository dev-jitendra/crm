<?php



namespace Symfony\Component\HttpFoundation\Session\Storage\Proxy;

use Symfony\Component\HttpFoundation\Session\Storage\Handler\StrictSessionHandler;


class SessionHandlerProxy extends AbstractProxy implements \SessionHandlerInterface, \SessionUpdateTimestampHandlerInterface
{
    protected $handler;

    public function __construct(\SessionHandlerInterface $handler)
    {
        $this->handler = $handler;
        $this->wrapper = $handler instanceof \SessionHandler;
        $this->saveHandlerName = $this->wrapper || ($handler instanceof StrictSessionHandler && $handler->isWrapper()) ? \ini_get('session.save_handler') : 'user';
    }

    public function getHandler(): \SessionHandlerInterface
    {
        return $this->handler;
    }

    

    public function open(string $savePath, string $sessionName): bool
    {
        return $this->handler->open($savePath, $sessionName);
    }

    public function close(): bool
    {
        return $this->handler->close();
    }

    public function read(string $sessionId): string|false
    {
        return $this->handler->read($sessionId);
    }

    public function write(string $sessionId, string $data): bool
    {
        return $this->handler->write($sessionId, $data);
    }

    public function destroy(string $sessionId): bool
    {
        return $this->handler->destroy($sessionId);
    }

    public function gc(int $maxlifetime): int|false
    {
        return $this->handler->gc($maxlifetime);
    }

    public function validateId(string $sessionId): bool
    {
        return !$this->handler instanceof \SessionUpdateTimestampHandlerInterface || $this->handler->validateId($sessionId);
    }

    public function updateTimestamp(string $sessionId, string $data): bool
    {
        return $this->handler instanceof \SessionUpdateTimestampHandlerInterface ? $this->handler->updateTimestamp($sessionId, $data) : $this->write($sessionId, $data);
    }
}
