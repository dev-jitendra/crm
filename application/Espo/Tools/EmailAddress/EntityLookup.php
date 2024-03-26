<?php


namespace Espo\Tools\EmailAddress;

use Espo\Entities\EmailAddress;
use Espo\ORM\Entity;
use Espo\ORM\EntityManager;
use Espo\Repositories\EmailAddress as EmailAddressRepository;

use RuntimeException;


class EntityLookup
{
    private EmailAddressRepository $internalRepository;

    public function __construct(
        private Repository $repository,
        EntityManager $entityManager
    ) {
        $repository = $entityManager->getRDBRepository(EmailAddress::ENTITY_TYPE);

        if (!$repository instanceof EmailAddressRepository) {
            throw new RuntimeException();
        }

        $this->internalRepository = $repository;
    }

    
    public function find(string $address): array
    {
        $emailAddress = $this->repository->getByAddress($address);

        if (!$emailAddress) {
            return [];
        }

        return $this->internalRepository->getEntityListByAddressId($emailAddress->getId());
    }

    
    public function findOne(string $address, ?array $order = null): ?Entity
    {
        if ($order) {
            $this->internalRepository->getEntityByAddress($address, null, $order);
        }

        return $this->internalRepository->getEntityByAddress($address);
    }
}
