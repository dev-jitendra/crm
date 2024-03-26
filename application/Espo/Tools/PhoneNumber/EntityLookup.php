<?php


namespace Espo\Tools\PhoneNumber;

use Espo\Entities\PhoneNumber;
use Espo\ORM\Entity;
use Espo\ORM\EntityManager;
use Espo\Repositories\PhoneNumber as PhoneNumberRepository;

use RuntimeException;


class EntityLookup
{
    private PhoneNumberRepository $internalRepository;

    public function __construct(
        private Repository $repository,
        EntityManager $entityManager
    ) {
        $repository = $entityManager->getRDBRepository(PhoneNumber::ENTITY_TYPE);

        if (!$repository instanceof PhoneNumberRepository) {
            throw new RuntimeException();
        }

        $this->internalRepository = $repository;
    }

    
    public function find(string $number): array
    {
        $phoneNumber = $this->repository->getByNumber($number);

        if (!$phoneNumber) {
            return [];
        }

        return $this->internalRepository->getEntityListByPhoneNumberId($phoneNumber->getId());
    }

    
    public function findOne(string $number, ?array $order = null): ?Entity
    {
        $phoneNumber = $this->repository->getByNumber($number);

        if (!$phoneNumber) {
            return null;
        }

        if ($order) {
            $this->internalRepository->getEntityByPhoneNumberId($phoneNumber->getId(), null, $order);
        }

        return $this->internalRepository->getEntityByPhoneNumberId($phoneNumber->getId());
    }
}
