<?php



namespace Symfony\Component\Routing\Matcher;

use Symfony\Component\Routing\Matcher\Dumper\CompiledUrlMatcherTrait;
use Symfony\Component\Routing\RequestContext;


class CompiledUrlMatcher extends UrlMatcher
{
    use CompiledUrlMatcherTrait;

    public function __construct(array $compiledRoutes, RequestContext $context)
    {
        $this->context = $context;
        [$this->matchHost, $this->staticRoutes, $this->regexpList, $this->dynamicRoutes, $this->checkCondition] = $compiledRoutes;
    }
}
