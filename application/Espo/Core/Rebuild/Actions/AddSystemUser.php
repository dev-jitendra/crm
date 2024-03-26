<?php


namespace Espo\Core\Rebuild\Actions;

use Espo\Core\Rebuild\RebuildAction;
use Espo\Core\Utils\Config;
use Espo\Core\Utils\SystemUser;
use Espo\Entities\User;
use Espo\ORM\EntityManager;

class AddSystemUser implements RebuildAction
{
    public function __construct(
        private EntityManager $entityManager,
        private Config $config,
        private SystemUser $systemUser
    ) {}

    public function process(): void
    {
        $repository = $this->entityManager->getRDBRepositoryByClass(User::class);

        $user = $repository
            ->where(['userName' => SystemUser::NAME])
            ->findOne();

        if ($user) {
            if ($user->getId() === $this->systemUser->getId()) {
                return;
            }

            $this->entityManager
                ->getQueryExecutor()
                ->execute(
                    $this->entityManager
                        ->getQueryBuilder()
                        ->delete()
                        ->from(User::ENTITY_TYPE)
                        ->where(['id' => $user->getId()])
                        ->build()
                );
        }

        
        $attributes = $this->config->get('systemUserAttributes');

        $user = $repository->getNew();

        $user->set('id', $this->systemUser->getId());
        $user->set('userName', SystemUser::NAME);
        $user->set('type', User::TYPE_SYSTEM);
        $user->set($attributes);

        $repository->save($user);
    }
}
