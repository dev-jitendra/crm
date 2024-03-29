<?php



namespace Symfony\Component\HttpFoundation\Session\Storage;

use Symfony\Component\HttpFoundation\Session\SessionBagInterface;


class MetadataBag implements SessionBagInterface
{
    public const CREATED = 'c';
    public const UPDATED = 'u';
    public const LIFETIME = 'l';

    private string $name = '__metadata';
    private string $storageKey;

    
    protected $meta = [self::CREATED => 0, self::UPDATED => 0, self::LIFETIME => 0];

    
    private int $lastUsed;

    private int $updateThreshold;

    
    public function __construct(string $storageKey = '_sf2_meta', int $updateThreshold = 0)
    {
        $this->storageKey = $storageKey;
        $this->updateThreshold = $updateThreshold;
    }

    
    public function initialize(array &$array)
    {
        $this->meta = &$array;

        if (isset($array[self::CREATED])) {
            $this->lastUsed = $this->meta[self::UPDATED];

            $timeStamp = time();
            if ($timeStamp - $array[self::UPDATED] >= $this->updateThreshold) {
                $this->meta[self::UPDATED] = $timeStamp;
            }
        } else {
            $this->stampCreated();
        }
    }

    
    public function getLifetime(): int
    {
        return $this->meta[self::LIFETIME];
    }

    
    public function stampNew(int $lifetime = null)
    {
        $this->stampCreated($lifetime);
    }

    
    public function getStorageKey(): string
    {
        return $this->storageKey;
    }

    
    public function getCreated(): int
    {
        return $this->meta[self::CREATED];
    }

    
    public function getLastUsed(): int
    {
        return $this->lastUsed;
    }

    
    public function clear(): mixed
    {
        
        return null;
    }

    
    public function getName(): string
    {
        return $this->name;
    }

    
    public function setName(string $name)
    {
        $this->name = $name;
    }

    private function stampCreated(int $lifetime = null): void
    {
        $timeStamp = time();
        $this->meta[self::CREATED] = $this->meta[self::UPDATED] = $this->lastUsed = $timeStamp;
        $this->meta[self::LIFETIME] = $lifetime ?? (int) \ini_get('session.cookie_lifetime');
    }
}
