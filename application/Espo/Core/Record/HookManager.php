<?php


namespace Espo\Core\Record;

use Espo\Core\Exceptions\BadRequest;
use Espo\Core\Exceptions\Conflict;
use Espo\Core\Exceptions\Forbidden;
use Espo\Core\Record\Hook\CreateHook;
use Espo\Core\Record\Hook\DeleteHook;
use Espo\Core\Record\Hook\LinkHook;
use Espo\Core\Record\Hook\ReadHook;
use Espo\Core\Record\Hook\SaveHook;
use Espo\Core\Record\Hook\UnlinkHook;
use Espo\Core\Record\Hook\UpdateHook;
use Espo\Core\Record\Hook\Provider;
use Espo\Core\Record\Hook\Type;
use Espo\ORM\Entity;

class HookManager
{
    public function __construct(private Provider $provider)
    {}

    
    public function processBeforeCreate(Entity $entity, CreateParams $params): void
    {
        foreach ($this->getBeforeCreateHookList($entity->getEntityType()) as $hook) {
            if ($hook instanceof SaveHook) {
                $hook->process($entity);

                continue;
            }

            $hook->process($entity, $params);
        }
    }

    public function processBeforeRead(Entity $entity, ReadParams $params): void
    {
        foreach ($this->getBeforeReadHookList($entity->getEntityType()) as $hook) {
            $hook->process($entity, $params);
        }
    }

    
    public function processBeforeUpdate(Entity $entity, UpdateParams $params): void
    {
        foreach ($this->getBeforeUpdateHookList($entity->getEntityType()) as $hook) {
            if ($hook instanceof SaveHook) {
                $hook->process($entity);

                continue;
            }

            $hook->process($entity, $params);
        }
    }

    
    public function processBeforeDelete(Entity $entity, DeleteParams $params): void
    {
        foreach ($this->getBeforeDeleteHookList($entity->getEntityType()) as $hook) {
            $hook->process($entity, $params);
        }
    }

    public function processBeforeLink(Entity $entity, string $link, Entity $foreignEntity): void
    {
        foreach ($this->getBeforeLinkHookList($entity->getEntityType()) as $hook) {
            $hook->process($entity, $link, $foreignEntity);
        }
    }

    public function processBeforeUnlink(Entity $entity, string $link, Entity $foreignEntity): void
    {
        foreach ($this->getBeforeUnlinkHookList($entity->getEntityType()) as $hook) {
            $hook->process($entity, $link, $foreignEntity);
        }
    }

    
    private function getBeforeReadHookList(string $entityType): array
    {
        
        return $this->provider->getList($entityType, Type::BEFORE_READ);
    }

    
    private function getBeforeCreateHookList(string $entityType): array
    {
        
        return $this->provider->getList($entityType, Type::BEFORE_CREATE);
    }

    
    private function getBeforeUpdateHookList(string $entityType): array
    {
        
        return $this->provider->getList($entityType, Type::BEFORE_UPDATE);
    }

    
    private function getBeforeDeleteHookList(string $entityType): array
    {
        
        return $this->provider->getList($entityType, Type::BEFORE_DELETE);
    }

    
    private function getBeforeLinkHookList(string $entityType): array
    {
        
        return $this->provider->getList($entityType, Type::BEFORE_LINK);
    }

    
    private function getBeforeUnlinkHookList(string $entityType): array
    {
        
        return $this->provider->getList($entityType, Type::BEFORE_UNLINK);
    }
}
