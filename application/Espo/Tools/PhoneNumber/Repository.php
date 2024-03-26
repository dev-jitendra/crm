<?php


namespace Espo\Tools\PhoneNumber;

use Espo\Entities\PhoneNumber;
use Espo\ORM\EntityManager;
use Espo\Repositories\PhoneNumber as PhoneNumberRepository;

use RuntimeException;


class Repository
{
    private PhoneNumberRepository $repository;

    public function __construct(EntityManager $entityManager)
    {
        $repository = $entityManager->getRDBRepository(PhoneNumber::ENTITY_TYPE);

        if (!$repository instanceof PhoneNumberRepository) {
            throw new RuntimeException();
        }

        $this->repository = $repository;
    }

    
    public function getByNumber(string $number): ?PhoneNumber
    {
        return $this->repository->getByNumber($number);
    }
}
