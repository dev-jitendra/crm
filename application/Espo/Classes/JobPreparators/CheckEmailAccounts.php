<?php


namespace Espo\Classes\JobPreparators;

use Espo\Core\Job\Preparator;
use Espo\Core\Job\Preparator\Data;
use Espo\ORM\EntityManager;
use Espo\Entities\EmailAccount;
use Espo\Core\Job\Preparator\CollectionHelper;

use DateTimeImmutable;

class CheckEmailAccounts implements Preparator
{
    
    public function __construct(
        private EntityManager $entityManager,
        private CollectionHelper $helper
    ) {}

    public function prepare(Data $data, DateTimeImmutable $executeTime): void
    {
        $collection = $this->entityManager
            ->getRDBRepositoryByClass(EmailAccount::class)
            ->join('assignedUser', 'assignedUserAdditional')
            ->where([
                'status' => EmailAccount::STATUS_ACTIVE,
                'useImap' => true,
                'assignedUserAdditional.isActive' => true,
            ])
            ->find();

        $this->helper->prepare($collection, $data, $executeTime);
    }
}
