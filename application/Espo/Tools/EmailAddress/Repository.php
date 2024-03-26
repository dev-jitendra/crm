<?php


namespace Espo\Tools\EmailAddress;

use Espo\Entities\EmailAddress;
use Espo\ORM\EntityManager;
use Espo\Repositories\EmailAddress as EmailAddressRepository;
use RuntimeException;


class Repository
{
    private EmailAddressRepository $repository;

    public function __construct(EntityManager $entityManager)
    {
        $repository = $entityManager->getRDBRepository(EmailAddress::ENTITY_TYPE);

        if (!$repository instanceof EmailAddressRepository) {
            throw new RuntimeException();
        }

        $this->repository = $repository;
    }

    
    public function getByAddress(string $address): ?EmailAddress
    {
        return $this->repository->getByAddress($address);
    }
}
