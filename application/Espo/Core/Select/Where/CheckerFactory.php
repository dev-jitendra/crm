<?php


namespace Espo\Core\Select\Where;

use Espo\Core\Exceptions\Error;
use Espo\Entities\User;
use Espo\Core\InjectableFactory;
use Espo\Core\Utils\Acl\UserAclManagerProvider;
use RuntimeException;

class CheckerFactory
{
    public function __construct(
        private InjectableFactory $injectableFactory,
        private UserAclManagerProvider $userAclManagerProvider
    ) {}

    public function create(string $entityType, User $user): Checker
    {
        try {
            $acl = $this->userAclManagerProvider
                ->get($user)
                ->createUserAcl($user);
        }
        catch (Error $e) {
            throw new RuntimeException($e->getMessage());
        }

        return $this->injectableFactory->createWith(Checker::class, [
            'entityType' => $entityType,
            'acl' => $acl,
        ]);
    }
}
