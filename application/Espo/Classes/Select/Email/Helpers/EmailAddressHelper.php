<?php


namespace Espo\Classes\Select\Email\Helpers;

use Espo\Entities\EmailAddress;
use Espo\ORM\EntityManager;

class EmailAddressHelper
{
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getEmailAddressIdByValue(string $value): ?string
    {
        $emailAddress = $this->entityManager
            ->getRDBRepository(EmailAddress::ENTITY_TYPE)
            ->where([
                'lower' => strtolower($value),
            ])
            ->findOne();

        if (!$emailAddress) {
            return null;
        }

        return $emailAddress->getId();
    }
}
