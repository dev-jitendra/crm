<?php


namespace Espo\Tools\Attachment;

use Espo\Core\Utils\Config;
use Espo\Core\Utils\Metadata;
use Espo\Entities\Attachment;

class DetailsObtainer
{
    private Metadata $metadata;
    private Config $config;

    public function __construct(
        Metadata $metadata,
        Config $config
    ) {
        $this->metadata = $metadata;
        $this->config = $config;
    }

    
    public static function getFileExtension(Attachment $attachment): ?string
    {
        $name = $attachment->getName() ?? '';

        return array_slice(explode('.', $name), -1)[0] ?? null;
    }

    
    public function getUploadMaxSize(Attachment $attachment): int
    {
        if ($attachment->getRole() === Attachment::ROLE_INLINE_ATTACHMENT) {
            return $this->config->get('inlineAttachmentUploadMaxSize') * 1024 * 1024;
        }

        $field = $attachment->getTargetField();
        $parentType = $attachment->getParentType() ?? $attachment->getRelatedType();

        if ($field && $parentType) {
            $maxSize = ($this->metadata
                ->get(['entityDefs', $parentType, 'fields', $field, 'maxFileSize']) ?? 0) * 1024 * 1024;

            if ($maxSize) {
                return $maxSize;
            }
        }

        return (int) $this->config->get('attachmentUploadMaxSize', 0) * 1024 * 1024;
    }

    
    public function getFieldType(Attachment $attachment): ?string
    {
        $field = $attachment->getTargetField();
        $entityType = $attachment->getParentType() ?? $attachment->getRelatedType();

        if (!$field || !$entityType) {
            return null;
        }

        return $this->metadata->get(['entityDefs', $entityType, 'fields', $field, 'type']);
    }
}
