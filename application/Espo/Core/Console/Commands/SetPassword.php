<?php


namespace Espo\Core\Console\Commands;

use Espo\Entities\User;
use Espo\Core\Console\Command;
use Espo\Core\Console\Command\Params;
use Espo\Core\Console\IO;
use Espo\Core\ORM\EntityManager;
use Espo\Core\Utils\PasswordHash;

class SetPassword implements Command
{
    public function __construct(private EntityManager $entityManager, private PasswordHash $passwordHash)
    {}

    public function run(Params $params, IO $io): void
    {
        $userName = $params->getArgument(0);

        if (!$userName) {
            $io->writeLine("Username must be specified as the first argument.");
            $io->setExitStatus(1);

            return;
        }

        $em = $this->entityManager;

        $user = $em->getRDBRepositoryByClass(User::class)
            ->where(['userName' => $userName])
            ->findOne();

        if (!$user) {
            $io->writeLine("User '{$userName}' not found.");
            $io->setExitStatus(1);

            return;
        }

        $userType = $user->getType();

        $allowedTypes = [
            User::TYPE_ADMIN,
            User::TYPE_SUPER_ADMIN,
            User::TYPE_PORTAL,
            User::TYPE_REGULAR,
        ];

        if (!in_array($userType, $allowedTypes)) {
            $io->writeLine("Can't set password for a user of the type '{$userType}'.");
            $io->setExitStatus(1);

            return;
        }

        $io->writeLine("Enter a new password:");

        $password = $io->readSecretLine();

        if (!$password) {
            $io->writeLine("Password cannot be empty.");
            $io->setExitStatus(1);

            return;
        }

        $user->set('password', $this->passwordHash->hash($password));

        $em->saveEntity($user);

        $io->writeLine("Password for user '{$userName}' has been changed.");
    }
}
