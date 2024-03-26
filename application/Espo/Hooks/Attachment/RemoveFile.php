<?php


namespace Espo\Hooks\Attachment;

use Espo\Core\FileStorage\Manager as FileStorageManager;
use Espo\Core\Hook\Hook\AfterRemove;
use Espo\Core\Utils\File\Manager as FileManager;
use Espo\Core\Utils\Metadata;
use Espo\Entities\Attachment;
use Espo\ORM\Entity;
use Espo\ORM\EntityManager;
use Espo\ORM\Repository\Option\RemoveOptions;


class RemoveFile implements AfterRemove
{
    public function __construct(
        private Metadata $metadata,
        private EntityManager $entityManager,
        private FileManager $fileManager,
        private FileStorageManager $fileStorageManager
    ) {}

    
    public function afterRemove(Entity $entity, RemoveOptions $options): void
    {
        $duplicateCount = $this->entityManager
            ->getRDBRepositoryByClass(Attachment::class)
            ->where([
                'OR' => [
                    'sourceId' => $entity->getSourceId(),
                    'id' => $entity->getSourceId(),
                ]
            ])
            ->count();

        if ($duplicateCount) {
            return;
        }

        if ($this->fileStorageManager->exists($entity)) {
            $this->fileStorageManager->unlink($entity);
        }

        $this->removeThumbs($entity);
    }

    private function removeThumbs(Attachment $entity): void
    {
        
        $typeList = $this->metadata->get(['app', 'image', 'resizableFileTypeList']) ?? [];

        if (!in_array($entity->getType(), $typeList)) {
            return;
        }

        
        $sizeList = array_keys($this->metadata->get(['app', 'image', 'sizes']) ?? []);

        foreach ($sizeList as $size) {
            $filePath = "data/upload/thumbs/{$entity->getSourceId()}_{$size}";

            if ($this->fileManager->isFile($filePath)) {
                $this->fileManager->removeFile($filePath);
            }
        }
    }
}
