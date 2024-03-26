<?php


namespace Espo\ORM;


class EventDispatcher
{
    
    private $data;

    private const METADATA_UPDATE = 'metadataUpdate';

    public function __construct()
    {
        $this->data = [
            self::METADATA_UPDATE => [],
        ];
    }

    public function subscribeToMetadataUpdate(callable $callback): void
    {
        $this->data[self::METADATA_UPDATE][] = $callback;
    }

    public function dispatchMetadataUpdate(): void
    {
        foreach ($this->data[self::METADATA_UPDATE] as $callback) {
            $callback();
        }
    }
}
