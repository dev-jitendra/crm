<?php


namespace Espo\Classes\FieldDuplicators;

use Espo\Core\Record\Duplicator\FieldDuplicator;
use Espo\ORM\Entity;
use Espo\ORM\EntityManager;

use Espo\Repositories\Attachment as AttachmentRepository;
use Espo\Entities\Attachment;

use stdClass;

class Wysiwyg implements FieldDuplicator
{
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function duplicate(Entity $entity, string $field): stdClass
    {
        $valueMap = (object) [];

        $contents = $entity->get($field);

        if (!$contents) {
            return $valueMap;
        }

        $matches = [];

        $matchResult = preg_match_all("/\?entryPoint=attachment&amp;id=([^&=\"']+)/", $contents, $matches);

        if (
            !$matchResult ||
            empty($matches[1]) ||
            !is_array($matches[1])
        ) {
            return $valueMap;
        }

        $attachmentIdList = $matches[1];

        
        $attachmentList = [];

        foreach ($attachmentIdList as $id) {
            
            $attachment = $this->entityManager->getEntity(Attachment::ENTITY_TYPE, $id);

            if (!$attachment) {
                continue;
            }

            $attachmentList[] = $attachment;
        }

        if (!count($attachmentList)) {
            return $valueMap;
        }

        
        $attachmentRepository = $this->entityManager->getRepository(Attachment::ENTITY_TYPE);

        foreach ($attachmentList as $attachment) {
            $copiedAttachment = $attachmentRepository->getCopiedAttachment($attachment);

            $copiedAttachment->set([
                'relatedId' => null,
                'relatedType' => $entity->getEntityType(),
                'field' => $field,
            ]);

            $this->entityManager->saveEntity($copiedAttachment);

            $contents = str_replace(
                '?entryPoint=attachment&amp;id=' . $attachment->getId(),
                '?entryPoint=attachment&amp;id=' . $copiedAttachment->getId(),
                $contents
            );
        }

        $valueMap->$field = $contents;

        return $valueMap;
    }
}
