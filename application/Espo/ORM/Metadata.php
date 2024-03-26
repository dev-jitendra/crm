<?php


namespace Espo\ORM;

use Espo\ORM\Defs\DefsData;

use InvalidArgumentException;


class Metadata
{
    
    private array $data;

    private Defs $defs;
    private DefsData $defsData;
    private EventDispatcher $eventDispatcher;

    public function __construct(
        private MetadataDataProvider $dataProvider,
        ?EventDispatcher $eventDispatcher = null
    ) {
        $this->data = $dataProvider->get();
        $this->defsData = new DefsData($this);
        $this->defs = new Defs($this->defsData);
        $this->eventDispatcher = $eventDispatcher ?? new EventDispatcher();
    }

    
    public function updateData(): void
    {
        $this->data = $this->dataProvider->get();

        $this->defsData->clearCache();

        $this->eventDispatcher->dispatchMetadataUpdate();
    }

    
    public function getDefs(): Defs
    {
        return $this->defs;
    }

    
    public function get(string $entityType, $key = null, $default = null)
    {
        if (!$this->has($entityType)) {
            return null;
        }

        $data = $this->data[$entityType];

        if ($key === null) {
            return $data;
        }

        return self::getValueByKey($data, $key, $default);
    }

    
    public function has(string $entityType): bool
    {
        return array_key_exists($entityType, $this->data);
    }

    
    public function getEntityTypeList(): array
    {
        return array_keys($this->data);
    }

    
    private static function getValueByKey(array $data, $key = null, $default = null)
    {
        if (!is_string($key) && !is_array($key) && !is_null($key)) { 
            throw new InvalidArgumentException();
        }

        if (is_null($key) || empty($key)) {
            return $data;
        }

        $path = $key;

        if (is_string($key)) {
            $path = explode('.', $key);
        }

        

        $item = $data;

        foreach ($path as $k) {
            if (!array_key_exists($k, $item)) {
                return $default;
            }

            $item = $item[$k];
        }

        return $item;
    }
}
