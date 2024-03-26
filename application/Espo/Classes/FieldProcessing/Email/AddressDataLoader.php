<?php


namespace Espo\Classes\FieldProcessing\Email;

use Espo\ORM\Entity;
use Espo\Core\FieldProcessing\Loader;
use Espo\Core\FieldProcessing\Loader\Params;
use Espo\Core\ORM\EntityManager;
use Espo\Repositories\Email as EmailRepository;
use Espo\Entities\Email;


class AddressDataLoader implements Loader
{
    public function __construct(private EntityManager $entityManager)
    {}

    
    public function process(Entity $entity, Params $params): void
    {
        
        $repository = $this->entityManager->getRepository(Email::ENTITY_TYPE);

        $repository->loadFromField($entity);
        $repository->loadToField($entity);
        $repository->loadCcField($entity);
        $repository->loadBccField($entity);
        $repository->loadReplyToField($entity);
        $repository->loadNameHash($entity);
    }
}
