<?php


namespace Espo\Classes\ConsoleCommands;

use Espo\Core\Console\Command;
use Espo\Core\Console\Command\Params;
use Espo\Core\Console\Exceptions\ArgumentNotSpecified;
use Espo\Core\Console\Exceptions\InvalidArgument;
use Espo\Core\Console\IO;
use Espo\Core\Exceptions\Error;
use Espo\Core\FieldProcessing\NextNumber\BeforeSaveProcessor;
use Espo\Core\ORM\Entity as CoreEntity;
use Espo\Core\ORM\Repository\Option\SaveOption;
use Espo\ORM\EntityManager;
use Espo\ORM\Query\Part\Order;

class PopulateNumbers implements Command
{
    private BeforeSaveProcessor $beforeSaveProcessor;
    private EntityManager $entityManager;

    public function __construct(
        BeforeSaveProcessor $beforeSaveProcessor,
        EntityManager $entityManager
    ) {
        $this->beforeSaveProcessor = $beforeSaveProcessor;
        $this->entityManager = $entityManager;
    }

    
    public function run(Params $params, IO $io): void
    {
        $entityType = $params->getArgument(0);
        $field = $params->getArgument(1);

        $orderBy = $params->getOption('orderBy') ?? 'createdAt';
        $order = strtoupper($params->getOption('order') ?? Order::ASC);

        if (!$entityType) {
            throw new ArgumentNotSpecified("No entity type argument.");
        }

        if (!$field) {
            throw new ArgumentNotSpecified("No field argument.");
        }

        if ($order !== Order::ASC && $order !== Order::DESC) {
            throw new InvalidArgument("Bad order option.");
        }

        $fieldType = $this->entityManager
            ->getDefs()
            ->getEntity($entityType)
            ->getField($field)
            ->getType();

        if ($fieldType !== 'number') {
            throw new InvalidArgument("Field `{$field}` is not of `number` type.");
        }

        $collection = $this->entityManager
            ->getRDBRepository($entityType)
            ->where([
                $field => null,
            ])
            ->order($orderBy, $order)
            ->sth()
            ->find();

        foreach ($collection as $i => $entity) {
            if (!$entity instanceof CoreEntity) {
                throw new Error();
            }

            $this->beforeSaveProcessor->processPopulate($entity, $field);
            $this->entityManager->saveEntity($entity, [SaveOption::IMPORT => true]);

            if ($i % 1000 === 0) {
                $io->write('.');
            }
        }

        $io->writeLine('');
        $io->writeLine('Done.');
    }
}
