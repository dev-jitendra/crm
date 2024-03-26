<?php


namespace Espo\Core\Acl\Cache;

use Espo\Core\Utils\File\Manager as FileManager;
use Espo\Entities\Portal;
use Espo\Entities\User;
use Espo\ORM\EntityManager;


class Clearer
{
    public function __construct(private FileManager $fileManager, private EntityManager $entityManager)
    {}

    public function clearForAllInternalUsers(): void
    {
        $this->fileManager->removeInDir('data/cache/application/acl');
        $this->fileManager->removeInDir('data/cache/application/aclMap');
    }

    public function clearForAllPortalUsers(): void
    {
        $this->fileManager->removeInDir('data/cache/application/aclPortal');
        $this->fileManager->removeInDir('data/cache/application/aclPortalMap');
    }

    public function clearForUser(User $user): void
    {
        if ($user->isPortal()) {
            $this->clearForPortalUser($user);

            return;
        }

        $part = $user->getId() . '.php';

        $this->fileManager->remove('data/cache/application/acl/' . $part);
        $this->fileManager->remove('data/cache/application/aclMap/' . $part);
    }

    private function clearForPortalUser(User $user): void
    {
        $portals = $this->entityManager
            ->getRDBRepositoryByClass(Portal::class)
            ->select('id')
            ->find();

        foreach ($portals as $portal) {
            $part = $portal->getId() . '/' . $user->getId() . '.php';

            $this->fileManager->remove('data/cache/application/aclPortal/' . $part);
            $this->fileManager->remove('data/cache/application/aclPortalMap/' . $part);
        }
    }
}
