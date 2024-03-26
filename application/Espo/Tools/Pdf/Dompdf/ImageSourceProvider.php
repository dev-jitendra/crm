<?php


namespace Espo\Tools\Pdf\Dompdf;

use Espo\Core\Exceptions\Forbidden;
use Espo\Core\FileStorage\Manager as FileStorageManager;
use Espo\Entities\Attachment;
use Espo\ORM\EntityManager;
use Espo\Tools\Attachment\Checker;

class ImageSourceProvider
{
    public function __construct(
        private EntityManager $entityManager,
        private Checker $checker,
        private FileStorageManager $fileStorageManager,
    ) {}

    public function get(string $id): ?string
    {
        
        $attachment = $this->entityManager->getEntityById(Attachment::ENTITY_TYPE, $id);

        if (!$attachment) {
            return null;
        }

        try {
            $this->checker->checkTypeImage($attachment);
        }
        catch (Forbidden) {
            return null;
        }

        $type = $attachment->getType();

        if (!$type) {
            return null;
        }

        $contents = $this->fileStorageManager->getContents($attachment);

        return 'data:' . $type . ';base64,' . base64_encode($contents);
    }
}
