<?php


namespace Espo\Tools\Attachment;

use Espo\Core\Exceptions\Forbidden;
use Espo\Core\Exceptions\ForbiddenSilent;
use Espo\Core\Utils\File\MimeType;
use Espo\Core\Utils\Metadata;
use Espo\Entities\Attachment;

class Checker
{
    private Metadata $metadata;
    private MimeType $mimeType;
    private DetailsObtainer $detailsObtainer;

    public function __construct(
        Metadata $metadata,
        MimeType $mimeType,
        DetailsObtainer $detailsObtainer
    ) {
        $this->metadata = $metadata;
        $this->mimeType = $mimeType;
        $this->detailsObtainer = $detailsObtainer;
    }

    
    public function checkType(Attachment $attachment): void
    {
        $field = $attachment->getTargetField();
        $entityType = $attachment->getParentType() ?? $attachment->getRelatedType();

        if (!$field || !$entityType) {
            return;
        }

        if (
            $this->detailsObtainer->getFieldType($attachment) === FieldType::IMAGE ||
            $attachment->getRole() === Attachment::ROLE_INLINE_ATTACHMENT
        ) {
            $this->checkTypeImage($attachment);

            return;
        }

        $extension = strtolower(DetailsObtainer::getFileExtension($attachment) ?? '');

        $mimeType = $this->mimeType->getMimeTypeByExtension($extension) ??
            $attachment->getType();

        
        $accept = $this->metadata->get(['entityDefs', $entityType, 'fields', $field, 'accept']) ?? [];

        if ($accept === []) {
            return;
        }

        $found = false;

        foreach ($accept as $token) {
            if (strtolower($token) === '.' . $extension) {
                $found = true;

                break;
            }

            if ($mimeType && MimeType::matchMimeTypeToAcceptToken($mimeType, $token)) {
                $found = true;

                break;
            }
        }

        if (!$found) {
            throw new ForbiddenSilent("Not allowed file type.");
        }
    }

    
    public function checkTypeImage(Attachment $attachment, ?string $filePath = null): void
    {
        $extension = DetailsObtainer::getFileExtension($attachment) ?? '';

        $mimeType = $this->mimeType->getMimeTypeByExtension($extension);

        
        $imageTypeList = $this->metadata->get(['app', 'image', 'allowedFileTypeList']) ?? [];

        if (!in_array($mimeType, $imageTypeList)) {
            throw new ForbiddenSilent("Not allowed file type.");
        }

        $setMimeType = $attachment->getType();

        if (strtolower($setMimeType ?? '') !== $mimeType) {
            throw new ForbiddenSilent("Passed type does not correspond to extension.");
        }

        $this->checkDetectedMimeType($attachment, $filePath);
    }

    
    private function checkDetectedMimeType(Attachment $attachment, ?string $filePath = null): void
    {
        
        if (!class_exists('\finfo') || !defined('FILEINFO_MIME_TYPE')) {
            return;
        }

        
        $contents = $attachment->get('contents');

        if (!$contents && !$filePath) {
            return;
        }

        $extension = DetailsObtainer::getFileExtension($attachment) ?? '';

        $mimeTypeList = $this->mimeType->getMimeTypeListByExtension($extension);

        $fileInfo = new \finfo(FILEINFO_MIME_TYPE);

        $detectedMimeType = $filePath ?
            $fileInfo->file($filePath) :
            $fileInfo->buffer($contents);

        if (!in_array($detectedMimeType, $mimeTypeList)) {
            throw new ForbiddenSilent("Detected mime type does not correspond to extension.");
        }
    }
}
