<?php


namespace Espo\Core\Console\Commands;

use Espo\Entities\User;
use Espo\ORM\EntityManager;
use Espo\Core\AclManager;
use Espo\Core\Console\Command;
use Espo\Core\Console\Command\Params;
use Espo\Core\Console\IO;
use Espo\Core\Container;
use Espo\Core\Portal\Application as PortalApplication;


class AclCheck implements Command
{
    public function __construct(private Container $container)
    {}

    public function run(Params $params, IO $io): void
    {
        $options = $params->getOptions();

        $userId = $options['userId'] ?? null;
        $scope = $options['scope'] ?? null;
        $id = $options['id'] ?? null;
        $action = $options['action'] ?? null;

        if (!$userId || !$scope || !$id) {
            return;
        }

        $container = $this->container;
        $entityManager = $this->container->getByClass(EntityManager::class);

        $user = $entityManager->getEntityById(User::ENTITY_TYPE, $userId);

        if (!$user) {
            return;
        }

        if ($user->isPortal()) {
            
            $portalIdList = $user->getLinkMultipleIdList('portals');

            foreach ($portalIdList as $portalId) {
                $application = new PortalApplication($portalId);
                $containerPortal = $application->getContainer();
                $entityManager = $containerPortal->getByClass(EntityManager::class);

                $user = $entityManager->getEntityById(User::ENTITY_TYPE, $userId);

                if (!$user) {
                    return;
                }

                $result = $this->check($user, $scope, $id, $action, $containerPortal);

                if ($result) {
                    $io->write('true');;

                    return;
                }
            }

            return;
        }

        if ($this->check($user, $scope, $id, $action, $container)) {
            $io->write('true');
        }
    }

    private function check(
        User $user,
        string $scope,
        string $id,
        ?string $action,
        Container $container
    ): bool {

        $entityManager = $container->getByClass(EntityManager::class);

        $entity = $entityManager->getEntityById($scope, $id);

        if (!$entity) {
            return false;
        }

        $aclManager = $container->getByClass(AclManager::class);

        return $aclManager->check($user, $entity, $action);
    }
}
