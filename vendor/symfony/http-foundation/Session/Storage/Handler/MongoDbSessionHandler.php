<?php



namespace Symfony\Component\HttpFoundation\Session\Storage\Handler;

use MongoDB\BSON\Binary;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Client;
use MongoDB\Collection;


class MongoDbSessionHandler extends AbstractSessionHandler
{
    private $mongo;
    private $collection;
    private array $options;

    
    public function __construct(Client $mongo, array $options)
    {
        if (!isset($options['database']) || !isset($options['collection'])) {
            throw new \InvalidArgumentException('You must provide the "database" and "collection" option for MongoDBSessionHandler.');
        }

        $this->mongo = $mongo;

        $this->options = array_merge([
            'id_field' => '_id',
            'data_field' => 'data',
            'time_field' => 'time',
            'expiry_field' => 'expires_at',
        ], $options);
    }

    public function close(): bool
    {
        return true;
    }

    
    protected function doDestroy(string $sessionId): bool
    {
        $this->getCollection()->deleteOne([
            $this->options['id_field'] => $sessionId,
        ]);

        return true;
    }

    public function gc(int $maxlifetime): int|false
    {
        return $this->getCollection()->deleteMany([
            $this->options['expiry_field'] => ['$lt' => new UTCDateTime()],
        ])->getDeletedCount();
    }

    
    protected function doWrite(string $sessionId, string $data): bool
    {
        $expiry = new UTCDateTime((time() + (int) \ini_get('session.gc_maxlifetime')) * 1000);

        $fields = [
            $this->options['time_field'] => new UTCDateTime(),
            $this->options['expiry_field'] => $expiry,
            $this->options['data_field'] => new Binary($data, Binary::TYPE_OLD_BINARY),
        ];

        $this->getCollection()->updateOne(
            [$this->options['id_field'] => $sessionId],
            ['$set' => $fields],
            ['upsert' => true]
        );

        return true;
    }

    public function updateTimestamp(string $sessionId, string $data): bool
    {
        $expiry = new UTCDateTime((time() + (int) \ini_get('session.gc_maxlifetime')) * 1000);

        $this->getCollection()->updateOne(
            [$this->options['id_field'] => $sessionId],
            ['$set' => [
                $this->options['time_field'] => new UTCDateTime(),
                $this->options['expiry_field'] => $expiry,
            ]]
        );

        return true;
    }

    
    protected function doRead(string $sessionId): string
    {
        $dbData = $this->getCollection()->findOne([
            $this->options['id_field'] => $sessionId,
            $this->options['expiry_field'] => ['$gte' => new UTCDateTime()],
        ]);

        if (null === $dbData) {
            return '';
        }

        return $dbData[$this->options['data_field']]->getData();
    }

    private function getCollection(): Collection
    {
        return $this->collection ??= $this->mongo->selectCollection($this->options['database'], $this->options['collection']);
    }

    protected function getMongo(): Client
    {
        return $this->mongo;
    }
}
