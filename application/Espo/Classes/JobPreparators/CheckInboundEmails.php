<?php


namespace Espo\Classes\JobPreparators;

use Espo\Core\Job\Preparator;
use Espo\Core\Job\Preparator\Data;
use Espo\ORM\EntityManager;
use Espo\Entities\InboundEmail;
use Espo\Core\Job\Preparator\CollectionHelper;

use DateTimeImmutable;

class CheckInboundEmails implements Preparator
{
    
    public function __construct(
        private EntityManager $entityManager,
        private CollectionHelper $helper
    ) {}

    public function prepare(Data $data, DateTimeImmutable $executeTime): void
    {
        $collection = $this->entityManager
            ->getRDBRepositoryByClass(InboundEmail::class)
            ->where([
                'status' => InboundEmail::STATUS_ACTIVE,
                'useImap' => true,
            ])
            ->find();

        $this->helper->prepare($collection, $data, $executeTime);
    }
}
