<?php


namespace Espo\Core\Console;

use Espo\Core\InjectableFactory;
use Espo\Core\Utils\Metadata;
use Espo\Core\Utils\Util;
use Espo\Core\Console\Exceptions\CommandNotSpecified;
use Espo\Core\Console\Exceptions\CommandNotFound;
use Espo\Core\Console\Command\Params;


class CommandManager
{
    private InjectableFactory $injectableFactory;
    private Metadata $metadata;

    private const DEFAULT_COMMAND = 'Help';
    private const DEFAULT_COMMAND_FLAG = 'help';

    public function __construct(InjectableFactory $injectableFactory, Metadata $metadata)
    {
        $this->injectableFactory = $injectableFactory;
        $this->metadata = $metadata;
    }

    
    public function run(array $argv): int
    {
        $command = $this->getCommandNameFromArgv($argv);
        $params = $this->createParamsFromArgv($argv);

        if (
            $command === null &&
            (
                $params->hasFlag(self::DEFAULT_COMMAND_FLAG) ||
                count($params->getFlagList()) === 0 &&
                count($params->getOptions()) === 0 &&
                count($params->getArgumentList()) === 0
            )
        ) {
            $command = self::DEFAULT_COMMAND;
        }

        if ($command === null) {
            throw new CommandNotSpecified("Command name is not specified.");
        }

        $io = new IO();

        $commandObj = $this->createCommand($command);

        if (!$commandObj instanceof Command) {
            
            assert(method_exists($commandObj, 'run'));

            $commandObj->run($params->getOptions(), $params->getFlagList(), $params->getArgumentList());

            return 0;
        }

        $commandObj->run($params, $io);

        return $io->getExitStatus();
    }

    
    private function getCommandNameFromArgv(array $argv): ?string
    {
        $command = isset($argv[1]) ? trim($argv[1]) : null;

        if ($command === null && count($argv) < 2) {
            return null;
        }

        if (!$command || !ctype_alpha($command[0])) {
            return null;
        }

        return ucfirst(Util::hyphenToCamelCase($command));
    }

    private function createCommand(string $command): object
    {
        $className = $this->getClassName($command);

        return $this->injectableFactory->create($className);
    }

    
    private function getClassName(string $command): string
    {
        
        $className =
            $this->metadata->get(['app', 'consoleCommands', lcfirst($command), 'className']);

        if ($className) {
            return $className;
        }

        $className = 'Espo\\Core\\Console\\Commands\\' . $command;

        if (!class_exists($className)) {
            throw new CommandNotFound("Command '" . Util::camelCaseToHyphen($command) ."' does not exist.");
        }

        
        return $className;
    }

    
    private function createParamsFromArgv(array $argv): Params
    {
        return Params::fromArgs(array_slice($argv, 1));
    }
}
