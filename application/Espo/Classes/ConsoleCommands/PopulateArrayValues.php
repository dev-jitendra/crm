<?php


namespace Espo\Classes\ConsoleCommands;

use Espo\Core\Console\Command;
use Espo\Core\Console\Command\Params;
use Espo\Core\Console\IO;
use Espo\Core\Exceptions\Error;
use Espo\Core\ORM\Entity as CoreEntity;
use Espo\Entities\ArrayValue;
use Espo\ORM\Entity;
use Espo\ORM\EntityManager;
use Espo\Repositories\ArrayValue as ArrayValueRepository;

class PopulateArrayValues implements Command
{
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    
    public function run(Params $params, IO $io): void
    {
        $entityType = $params->getArgument(0);
        $field = $params->getArgument(1);

        if (!$entityType || !$field) {
            throw new Error("Entity type and field should be passed as arguments.");
        }

        if (!$this->entityManager->hasRepository($entityType)) {
            throw new Error("Bad entity type.");
        }

        $defs = $this->entityManager->getDefs()->getEntity($entityType);

        if (!$defs->hasAttribute($field)) {
            throw new Error("Bad field.");
        }

        if ($defs->getAttribute($field)->getType() !== Entity::JSON_ARRAY) {
            throw new Error("Non-array field.");
        }

        if ($defs->getAttribute($field)->isNotStorable()) {
            throw new Error("Not-storable field.");
        }

        if (!$defs->getAttribute($field)->getParam('storeArrayValues')) {
            throw new Error("Array values disabled for the field..");
        }

        $collection = $this->entityManager
            ->getRDBRepository($entityType)
            ->sth()
            ->find();

        
        $repository = $this->entityManager->getRepository(ArrayValue::ENTITY_TYPE);

        foreach ($collection as $i => $entity) {
            if (!$entity instanceof CoreEntity) {
                throw new Error();
            }

            $repository->storeEntityAttribute($entity, $field);

            if ($i % 1000 === 0) {
                $io->write('.');
            }
        }

        $io->writeLine('');
        $io->writeLine('Done.');
    }
}
