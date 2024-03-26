<?php


namespace Espo\Classes\ConsoleCommands;

use Espo\Core\Console\Command;
use Espo\Core\Console\Command\Params;
use Espo\Core\Console\IO;
use Espo\Core\Utils\Config;
use Espo\Entities\User;
use Espo\ORM\EntityManager;

use RuntimeException;

class CreateAdminUser implements Command
{
    public function __construct(
        private EntityManager $entityManager,
        private Config $config
    ) {}

    public function run(Params $params, IO $io): void
    {
        $userName = $params->getArgument(0);

        if (!$userName) {
            $io->writeLine("A username must be specified as the first argument.");
            $io->setExitStatus(1);

            return;
        }

        
        $regExp = $this->config->get('userNameRegularExpression');

        if (!$regExp) {
            throw new RuntimeException("No `userNameRegularExpression` in config.");
        }

        if (
            str_contains($userName, ' ') ||
            preg_replace("/{$regExp}/", '_', $userName) !== $userName
        ) {
            $io->writeLine("Not allowed username.");
            $io->setExitStatus(1);

            return;
        }

        $repository = $this->entityManager->getRDBRepositoryByClass(User::class);

        $existingUser = $repository
            ->where(['userName' => $userName])
            ->findOne();

        if ($existingUser) {
            $io->writeLine("A user with the same username already exists.");
            $io->setExitStatus(1);

            return;
        }

        $user = $repository->getNew();

        $user->set('userName', $userName);
        $user->set('type', User::TYPE_ADMIN);
        $user->set('name', $userName);

        $repository->save($user);

        $message = "The user '{$userName}' has been created. " .
            "Set password with the command: `bin/command set-password {$userName}`.";

        $io->writeLine($message);
    }
}
