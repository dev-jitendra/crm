<?php


namespace Espo\Classes\FieldProcessing\Portal;

use Espo\ORM\Entity;

use Espo\Repositories\Portal as PortalRepository;
use Espo\Entities\Portal;
use Espo\Core\FieldProcessing\Loader;
use Espo\Core\FieldProcessing\Loader\Params;
use Espo\Core\ORM\EntityManager;


class UrlLoader implements Loader
{
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function process(Entity $entity, Params $params): void
    {
        

        $this->getPortalRepository()->loadUrlField($entity);
    }

    private function getPortalRepository(): PortalRepository
    {
        
        return $this->entityManager->getRepository('Portal');
    }
}
