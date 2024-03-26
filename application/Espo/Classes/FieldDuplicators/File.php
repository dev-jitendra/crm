<?php


namespace Espo\Classes\FieldDuplicators;

use Espo\Core\Record\Duplicator\FieldDuplicator;
use Espo\ORM\Entity;
use Espo\ORM\EntityManager;

use Espo\Repositories\Attachment as AttachmentRepository;
use Espo\Entities\Attachment;

use stdClass;

class File implements FieldDuplicator
{
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function duplicate(Entity $entity, string $field): stdClass
    {
        $valueMap = (object) [];

        
        $attachment = $this->entityManager
            ->getRDBRepository($entity->getEntityType())
            ->getRelation($entity, $field)
            ->findOne();

        if (!$attachment) {
            return $valueMap;
        }

        
        $attachmentRepository = $this->entityManager->getRepository(Attachment::ENTITY_TYPE);

        $copiedAttachment = $attachmentRepository->getCopiedAttachment($attachment);

        $idAttribute = $field . 'Id';

        $valueMap->$idAttribute = $copiedAttachment->getId();

        return $valueMap;
    }
}
