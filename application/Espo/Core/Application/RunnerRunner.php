<?php


namespace Espo\Core\Application;

use Espo\Core\Utils\Log;
use Espo\Core\ApplicationUser;
use Espo\Core\InjectableFactory;
use Espo\Core\Application\Exceptions\RunnerException;
use Espo\Core\Application\Runner\Params;

use ReflectionClass;


class RunnerRunner
{
    public function __construct(
        private Log $log,
        private ApplicationUser $applicationUser,
        private InjectableFactory $injectableFactory
    ) {}

    
    public function run(string $className, ?Params $params = null): void
    {
        if (!class_exists($className)) {
            $this->log->error("Application runner '{$className}' does not exist.");

            throw new RunnerException();
        }

        $class = new ReflectionClass($className);

        if (
            $class->getStaticPropertyValue('cli', false) &&
            !str_starts_with(php_sapi_name() ?: '', 'cli')
        ) {
            throw new RunnerException("Can be run only via CLI.");
        }

        if ($class->getStaticPropertyValue('setupSystemUser', false)) {
            $this->applicationUser->setupSystemUser();
        }

        $runner = $this->injectableFactory->create($className);

        if ($runner instanceof RunnerParameterized) {
            $runner->run($params ?? Params::create());

            return;
        }

        $runner->run();
    }
}
