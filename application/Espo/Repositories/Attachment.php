<?php


namespace Espo\Repositories;

use Espo\ORM\Entity;
use Espo\Entities\Attachment as AttachmentEntity;
use Espo\Core\Repositories\Database;
use Espo\Core\FileStorage\Storages\EspoUploadDir;
use Espo\Core\Di;

use Psr\Http\Message\StreamInterface;


class Attachment extends Database implements
    Di\FileStorageManagerAware,
    Di\ConfigAware
{
    use Di\FileStorageManagerSetter;
    use Di\ConfigSetter;

    
    protected function beforeSave(Entity $entity, array $options = [])
    {
        parent::beforeSave($entity, $options);

        if ($entity->isNew()) {
            $this->processBeforeSaveNew($entity);
        }
    }

    protected function processBeforeSaveNew(AttachmentEntity $entity): void
    {
        if ($entity->isBeingUploaded()) {
            $entity->set('storage', EspoUploadDir::NAME);
        }

        if (!$entity->getStorage()) {
            $defaultStorage = $this->config->get('defaultFileStorage');

            $entity->set('storage', $defaultStorage);
        }

        $contents = $entity->get('contents');

        if (is_null($contents)) {
            return;
        }

        if (!$entity->isBeingUploaded()) {
            $entity->set('size', strlen($contents));
        }

        $this->fileStorageManager->putContents($entity, $contents);
    }

    
    public function getCopiedAttachment(AttachmentEntity $entity, ?string $role = null): AttachmentEntity
    {
        $attachment = $this->getNew();

        $attachment->set([
            'sourceId' => $entity->getSourceId(),
            'name' => $entity->getName(),
            'type' => $entity->getType(),
            'size' => $entity->getSize(),
            'role' => $entity->getRole(),
        ]);

        if ($role) {
            $attachment->set('role', $role);
        }

        $this->save($attachment);

        return $attachment;
    }

    public function getContents(AttachmentEntity $entity): string
    {
        return $this->fileStorageManager->getContents($entity);
    }

    public function getStream(AttachmentEntity $entity): StreamInterface
    {
        return $this->fileStorageManager->getStream($entity);
    }

    
    public function getSize(AttachmentEntity $entity): int
    {
        return $this->fileStorageManager->getSize($entity);
    }

    public function getFilePath(AttachmentEntity $entity): string
    {
        return $this->fileStorageManager->getLocalFilePath($entity);
    }
}
