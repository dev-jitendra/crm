<?php


namespace Espo\Core\FileStorage;

use Espo\Entities\Attachment as AttachmentEntity;

use RuntimeException;

class AttachmentEntityWrapper implements Attachment
{
    private AttachmentEntity $attachment;

    public function __construct(AttachmentEntity $attachment)
    {
        if (!$attachment->getSourceId()) {
            throw new RuntimeException("Attachment w/o a source ID.");
        }

        $this->attachment = $attachment;
    }

    public function getSourceId(): string
    {
        $sourceId = $this->attachment->getSourceId();

        if (!$sourceId) {
            throw new RuntimeException("Attachment w/o a source ID.");
        }

        return $sourceId;
    }
}
