<?php



namespace Symfony\Component\Routing\Generator\Dumper;

use Symfony\Component\Routing\Exception\RouteCircularReferenceException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Matcher\Dumper\CompiledUrlMatcherDumper;


class CompiledUrlGeneratorDumper extends GeneratorDumper
{
    public function getCompiledRoutes(): array
    {
        $compiledRoutes = [];
        foreach ($this->getRoutes()->all() as $name => $route) {
            $compiledRoute = $route->compile();

            $compiledRoutes[$name] = [
                $compiledRoute->getVariables(),
                $route->getDefaults(),
                $route->getRequirements(),
                $compiledRoute->getTokens(),
                $compiledRoute->getHostTokens(),
                $route->getSchemes(),
                [],
            ];
        }

        return $compiledRoutes;
    }

    public function getCompiledAliases(): array
    {
        $routes = $this->getRoutes();
        $compiledAliases = [];
        foreach ($routes->getAliases() as $name => $alias) {
            $deprecations = $alias->isDeprecated() ? [$alias->getDeprecation($name)] : [];
            $currentId = $alias->getId();
            $visited = [];
            while (null !== $alias = $routes->getAlias($currentId) ?? null) {
                if (false !== $searchKey = array_search($currentId, $visited)) {
                    $visited[] = $currentId;

                    throw new RouteCircularReferenceException($currentId, \array_slice($visited, $searchKey));
                }

                if ($alias->isDeprecated()) {
                    $deprecations[] = $deprecation = $alias->getDeprecation($currentId);
                    trigger_deprecation($deprecation['package'], $deprecation['version'], $deprecation['message']);
                }

                $visited[] = $currentId;
                $currentId = $alias->getId();
            }

            if (null === $target = $routes->get($currentId)) {
                throw new RouteNotFoundException(sprintf('Target route "%s" for alias "%s" does not exist.', $currentId, $name));
            }

            $compiledTarget = $target->compile();

            $compiledAliases[$name] = [
                $compiledTarget->getVariables(),
                $target->getDefaults(),
                $target->getRequirements(),
                $compiledTarget->getTokens(),
                $compiledTarget->getHostTokens(),
                $target->getSchemes(),
                $deprecations,
            ];
        }

        return $compiledAliases;
    }

    
    public function dump(array $options = []): string
    {
        return <<<EOF
<?php



return [{$this->generateDeclaredRoutes()}
];

EOF;
    }

    
    private function generateDeclaredRoutes(): string
    {
        $routes = '';
        foreach ($this->getCompiledRoutes() as $name => $properties) {
            $routes .= sprintf("\n    '%s' => %s,", $name, CompiledUrlMatcherDumper::export($properties));
        }

        foreach ($this->getCompiledAliases() as $alias => $properties) {
            $routes .= sprintf("\n    '%s' => %s,", $alias, CompiledUrlMatcherDumper::export($properties));
        }

        return $routes;
    }
}
