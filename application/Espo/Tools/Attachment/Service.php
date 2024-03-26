<?php


namespace Espo\Tools\Attachment;

use Espo\Core\Exceptions\Error;
use Espo\Core\Exceptions\Forbidden;
use Espo\Core\Exceptions\NotFound;
use Espo\Core\Record\ServiceContainer;
use Espo\Entities\Attachment;
use Espo\ORM\EntityManager;
use Espo\Repositories\Attachment as AttachmentRepository;

class Service
{
    private ServiceContainer $recordServiceContainer;
    private EntityManager $entityManager;
    private AccessChecker $accessChecker;

    public function __construct(
        ServiceContainer $recordServiceContainer,
        EntityManager $entityManager,
        AccessChecker $accessChecker
    ) {
        $this->recordServiceContainer = $recordServiceContainer;
        $this->entityManager = $entityManager;
        $this->accessChecker = $accessChecker;
    }

    
    public function getFileData(string $id): FileData
    {
        
        $attachment = $this->recordServiceContainer
            ->get(Attachment::ENTITY_TYPE)
            ->getEntity($id);

        if (!$attachment) {
            throw new NotFound();
        }

        return new FileData(
            $attachment->getName(),
            $attachment->getType(),
            $this->getAttachmentRepository()->getStream($attachment),
            $this->getAttachmentRepository()->getSize($attachment)
        );
    }

    
    public function copy(string $id, FieldData $data): Attachment
    {
        $this->accessChecker->check($data);

        
        $attachment = $this->recordServiceContainer
            ->get(Attachment::ENTITY_TYPE)
            ->getEntity($id);

        if (!$attachment) {
            throw new NotFound();
        }

        $copied = $this->getAttachmentRepository()->getCopiedAttachment($attachment);

        $copied->set('parentType', $data->getParentType());
        $copied->set('relatedType', $data->getRelatedType());
        $copied->setTargetField($data->getField());
        $copied->setRole(Attachment::ROLE_ATTACHMENT);

        $this->getAttachmentRepository()->save($copied);

        return $copied;
    }

    private function getAttachmentRepository(): AttachmentRepository
    {
        
        return $this->entityManager->getRepositoryByClass(Attachment::class);
    }
}
