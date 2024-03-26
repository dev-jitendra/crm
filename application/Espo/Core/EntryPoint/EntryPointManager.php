<?php


namespace Espo\Core\EntryPoint;

use Espo\Core\Exceptions\NotFound;
use Espo\Core\Api\Request;
use Espo\Core\Api\Response;
use Espo\Core\Exceptions\NotFoundSilent;
use Espo\Core\InjectableFactory;
use Espo\Core\Utils\ClassFinder;


class EntryPointManager
{
    public function __construct(
        private InjectableFactory $injectableFactory,
        private ClassFinder $classFinder
    ) {}

    
    public function checkAuthRequired(string $name): bool
    {
        $className = $this->getClassName($name);

        if (!$className) {
            throw new NotFoundSilent("Entry point '{$name}' not found.");
        }

        $noAuth = false;

        if (isset($className::$noAuth)) {
            $noAuth = $className::$noAuth;
        }

        if ($noAuth) {
            return false;
        }

        
        return $className::$authRequired ?? true;
    }

    
    public function run(string $name, Request $request, Response $response): void
    {
        $className = $this->getClassName($name);

        if (!$className) {
            throw new NotFoundSilent("Entry point '{$name}' not found.");
        }

        $entryPoint = $this->injectableFactory->create($className);

        $entryPoint->run($request, $response);
    }

    
    private function getClassName(string $name): ?string
    {
        
        return $this->classFinder->find('EntryPoints', ucfirst($name));
    }
}
