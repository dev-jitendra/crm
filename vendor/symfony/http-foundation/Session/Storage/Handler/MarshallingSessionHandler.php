<?php



namespace Symfony\Component\HttpFoundation\Session\Storage\Handler;

use Symfony\Component\Cache\Marshaller\MarshallerInterface;


class MarshallingSessionHandler implements \SessionHandlerInterface, \SessionUpdateTimestampHandlerInterface
{
    private $handler;
    private $marshaller;

    public function __construct(AbstractSessionHandler $handler, MarshallerInterface $marshaller)
    {
        $this->handler = $handler;
        $this->marshaller = $marshaller;
    }

    public function open(string $savePath, string $name): bool
    {
        return $this->handler->open($savePath, $name);
    }

    public function close(): bool
    {
        return $this->handler->close();
    }

    public function destroy(string $sessionId): bool
    {
        return $this->handler->destroy($sessionId);
    }

    public function gc(int $maxlifetime): int|false
    {
        return $this->handler->gc($maxlifetime);
    }

    public function read(string $sessionId): string
    {
        return $this->marshaller->unmarshall($this->handler->read($sessionId));
    }

    public function write(string $sessionId, string $data): bool
    {
        $failed = [];
        $marshalledData = $this->marshaller->marshall(['data' => $data], $failed);

        if (isset($failed['data'])) {
            return false;
        }

        return $this->handler->write($sessionId, $marshalledData['data']);
    }

    public function validateId(string $sessionId): bool
    {
        return $this->handler->validateId($sessionId);
    }

    public function updateTimestamp(string $sessionId, string $data): bool
    {
        return $this->handler->updateTimestamp($sessionId, $data);
    }
}
