<?php


namespace Espo\EntryPoints;

use Espo\Core\Utils\Metadata;
use Espo\Entities\Attachment as AttachmentEntity;
use Espo\Core\Acl;
use Espo\Core\Api\Request;
use Espo\Core\Api\Response;
use Espo\Core\EntryPoint\EntryPoint;
use Espo\Core\Exceptions\BadRequest;
use Espo\Core\Exceptions\Forbidden;
use Espo\Core\Exceptions\NotFound;
use Espo\Core\FileStorage\Manager as FileStorageManager;
use Espo\Core\ORM\EntityManager;

class Attachment implements EntryPoint
{
    public function __construct(
        private FileStorageManager $fileStorageManager,
        private EntityManager $entityManager,
        private Acl $acl,
        private Metadata $metadata
    ) {}

    public function run(Request $request, Response $response): void
    {
        $id = $request->getQueryParam('id');

        if (!$id) {
            throw new BadRequest();
        }

        $attachment = $this->entityManager
            ->getRDBRepositoryByClass(AttachmentEntity::class)
            ->getById($id);

        if (!$attachment) {
            throw new NotFound();
        }

        if (!$this->acl->checkEntity($attachment)) {
            throw new Forbidden();
        }

        if (!$this->fileStorageManager->exists($attachment)) {
            throw new NotFound();
        }

        $fileType = $attachment->getType();

        if (!in_array($fileType, $this->getAllowedFileTypeList())) {
            throw new Forbidden("Not allowed file type '{$fileType}'.");
        }

        if ($attachment->isBeingUploaded()) {
            throw new Forbidden("Attachment is being-uploaded.");
        }

        if ($fileType) {
            $response->setHeader('Content-Type', $fileType);
        }

        $stream = $this->fileStorageManager->getStream($attachment);

        $size = $stream->getSize() ?? $this->fileStorageManager->getSize($attachment);

        $response
            ->setHeader('Pragma', 'public')
            ->setHeader('Content-Length', (string) $size)
            ->setHeader('Content-Security-Policy', "default-src 'self'")
            ->setBody($stream);
    }

    
    private function getAllowedFileTypeList(): array
    {
        return $this->metadata->get(['app', 'image', 'allowedFileTypeList']) ?? [];
    }
}
