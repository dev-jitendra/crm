<?php


namespace Espo\Tools\Pdf\Jobs;

use Espo\Core\Job\Job;
use Espo\Core\Job\Job\Data;
use Espo\Entities\Attachment;
use Espo\ORM\EntityManager;
use RuntimeException;

class RemoveMassFile implements Job
{
    private const ATTACHMENT_MASS_PDF_ROLE = 'Mass Pdf';
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function run(Data $data): void
    {
        $id = $data->getTargetId();

        if (!$id) {
            throw new RuntimeException();
        }

        
        $attachment = $this->entityManager->getEntityById(Attachment::ENTITY_TYPE, $id);

        if (!$attachment) {
            return;
        }

        if ($attachment->getRole() !== self::ATTACHMENT_MASS_PDF_ROLE) {
            throw new RuntimeException();
        }

        $this->entityManager->removeEntity($attachment);
    }
}
