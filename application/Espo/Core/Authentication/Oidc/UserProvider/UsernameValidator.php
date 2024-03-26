<?php


namespace Espo\Core\Authentication\Oidc\UserProvider;

use Espo\Entities\User;
use Espo\ORM\EntityManager;
use RuntimeException;

class UsernameValidator
{
    public function __construct(private EntityManager $entityManager) {}

    public function validate(string $username): void
    {
        $maxLength = $this->entityManager
            ->getDefs()
            ->getEntity(User::ENTITY_TYPE)
            ->getAttribute('userName')
            ->getLength();

        if ($maxLength && $maxLength < strlen($username)) {
            throw new RuntimeException("Value in username claim exceeds max length of `{$maxLength}`. " .
                "Increase maxLength parameter for User.userName field (up to 255).");
        }
    }
}
