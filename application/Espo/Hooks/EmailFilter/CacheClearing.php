<?php


namespace Espo\Hooks\EmailFilter;

use Espo\Core\Hook\Hook\AfterRemove;
use Espo\Core\Hook\Hook\AfterSave;
use Espo\Core\Utils\DataCache;
use Espo\Entities\EmailFilter;
use Espo\Entities\User;
use Espo\ORM\Entity;
use Espo\ORM\Repository\Option\RemoveOptions;
use Espo\ORM\Repository\Option\SaveOptions;


class CacheClearing implements AfterSave, AfterRemove
{
    private const CACHE_KEY = 'emailFilters';

    public function __construct(private DataCache $dataCache) {}

    
    public function afterSave(Entity $entity, SaveOptions $options): void
    {
        $this->processEntity($entity);
    }

    
    public function afterRemove(Entity $entity, RemoveOptions $options): void
    {
        $this->processEntity($entity);
    }

    private function processEntity(EmailFilter $entity): void
    {
        if ($entity->getParentType() !== User::ENTITY_TYPE || !$entity->getParentId()) {
            return;
        }

        $cacheKey = $this->composeCacheKey($entity->getParentId());

        $this->dataCache->clear($cacheKey);
    }

    private function composeCacheKey(string $userId): string
    {
        return self::CACHE_KEY . '/' . $userId;
    }
}
