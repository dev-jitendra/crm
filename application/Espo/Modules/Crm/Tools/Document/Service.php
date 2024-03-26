<?php


namespace Espo\Modules\Crm\Tools\Document;

use Espo\Core\Exceptions\Error;
use Espo\Core\Exceptions\Forbidden;
use Espo\Core\Exceptions\NotFound;
use Espo\Core\Record\ServiceContainer;
use Espo\Entities\Attachment;
use Espo\Modules\Crm\Entities\Document;
use Espo\ORM\EntityManager;
use Espo\Repositories\Attachment as AttachmentRepository;
use Espo\Tools\Attachment\AccessChecker as AttachmentAccessChecker;
use Espo\Tools\Attachment\FieldData;

class Service
{
    private EntityManager $entityManager;
    private AttachmentAccessChecker $attachmentAccessChecker;
    private ServiceContainer $serviceContainer;

    public function __construct(
        EntityManager $entityManager,
        AttachmentAccessChecker $attachmentAccessChecker,
        ServiceContainer $serviceContainer
    ) {
        $this->entityManager = $entityManager;
        $this->attachmentAccessChecker = $attachmentAccessChecker;
        $this->serviceContainer = $serviceContainer;
    }

    
    public function copyAttachment(string $id, FieldData $fieldData): Attachment
    {
        
        $entity = $this->serviceContainer
            ->getByClass(Document::class)
            ->getEntity($id);

        if (!$entity) {
            throw new NotFound();
        }

        $this->attachmentAccessChecker->check($fieldData);

        $attachmentId = $entity->getFileId();

        if (!$attachmentId) {
            throw new Error("No file.");
        }

        $attachment = $this->copyAttachmentById($attachmentId, $fieldData);

        if (!$attachment) {
            throw new Error("No file.");
        }

        return $attachment;
    }

    private function copyAttachmentById(string $attachmentId, FieldData $fieldData): ?Attachment
    {
        
        $attachment = $this->entityManager
            ->getRDBRepositoryByClass(Attachment::class)
            ->getById($attachmentId);

        if (!$attachment) {
            return null;
        }

        $copied = $this->getAttachmentRepository()->getCopiedAttachment($attachment);

        $copied->set('parentType', $fieldData->getParentType());
        $copied->set('relatedType', $fieldData->getRelatedType());
        $copied->setTargetField($fieldData->getField());
        $copied->setRole(Attachment::ROLE_ATTACHMENT);

        $this->getAttachmentRepository()->save($copied);

        return $copied;
    }

    private function getAttachmentRepository(): AttachmentRepository
    {
        
        return $this->entityManager->getRepositoryByClass(Attachment::class);
    }
}
