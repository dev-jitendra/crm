<?php

namespace Doctrine\DBAL\Tools\Console;

use Composer\InstalledVersions;
use Doctrine\DBAL\Tools\Console\Command\ReservedWordsCommand;
use Doctrine\DBAL\Tools\Console\Command\RunSqlCommand;
use Exception;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;

use function assert;


class ConsoleRunner
{
    
    public static function run(ConnectionProvider $connectionProvider, $commands = [])
    {
        $version = InstalledVersions::getVersion('doctrine/dbal');
        assert($version !== null);

        $cli = new Application('Doctrine Command Line Interface', $version);

        $cli->setCatchExceptions(true);
        self::addCommands($cli, $connectionProvider);
        $cli->addCommands($commands);
        $cli->run();
    }

    
    public static function addCommands(Application $cli, ConnectionProvider $connectionProvider)
    {
        $cli->addCommands([
            new RunSqlCommand($connectionProvider),
            new ReservedWordsCommand($connectionProvider),
        ]);
    }

    
    public static function printCliConfigTemplate()
    {
        echo <<<'HELP'
You are missing a "cli-config.php" or "config/cli-config.php" file in your
project, which is required to get the Doctrine-DBAL Console working. You can use the
following sample as a template:

<?php
use Doctrine\DBAL\Tools\Console\ConnectionProvider\SingleConnectionProvider;





$connection = getDBALConnection();



return new SingleConnectionProvider($connection);

HELP;
    }
}
