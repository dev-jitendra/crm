<?php


namespace Espo\Core\Acl\AssignmentChecker;

use Espo\ORM\Entity;
use Espo\Entities\User;
use Espo\Core\Acl\AssignmentChecker;

class AssignmentCheckerManager
{
    
    private $checkerCache = [];

    public function __construct(private AssignmentCheckerFactory $factory)
    {}

    public function check(User $user, Entity $entity): bool
    {
        $entityType = $entity->getEntityType();

        $checker = $this->getChecker($entityType);

        return $checker->check($user, $entity);
    }

    
    private function getChecker(string $entityType): AssignmentChecker
    {
        if (!array_key_exists($entityType, $this->checkerCache)) {
            $this->loadChecker($entityType);
        }

        return $this->checkerCache[$entityType];
    }

    private function loadChecker(string $entityType): void
    {
        $this->checkerCache[$entityType] = $this->factory->create($entityType);
    }
}
