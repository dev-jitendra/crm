<?php


namespace Espo\Classes\FieldProcessing\Import;

use Espo\Entities\Import;
use Espo\ORM\Entity;
use Espo\Core\FieldProcessing\Loader;
use Espo\Core\FieldProcessing\Loader\Params;
use Espo\Core\ORM\EntityManager;

use Espo\Repositories\Import as ImportRepository;


class CountsLoader implements Loader
{
    public function __construct(private EntityManager $entityManager)
    {}

    public function process(Entity $entity, Params $params): void
    {
        
        $repository = $this->entityManager->getRepository('Import');

        $importedCount = $repository->countResultRecords($entity, 'imported');
        $duplicateCount = $repository->countResultRecords($entity, 'duplicates');
        $updatedCount = $repository->countResultRecords($entity, 'updated');

        $entity->set([
            'importedCount' => $importedCount,
            'duplicateCount' => $duplicateCount,
            'updatedCount' => $updatedCount,
        ]);
    }
}
