<?php



namespace Symfony\Component\Routing\Matcher;

use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;
use Symfony\Contracts\Service\ServiceProviderInterface;


class ExpressionLanguageProvider implements ExpressionFunctionProviderInterface
{
    private $functions;

    public function __construct(ServiceProviderInterface $functions)
    {
        $this->functions = $functions;
    }

    
    public function getFunctions(): array
    {
        $functions = [];

        foreach ($this->functions->getProvidedServices() as $function => $type) {
            $functions[] = new ExpressionFunction(
                $function,
                static function (...$args) use ($function) {
                    return sprintf('($context->getParameter(\'_functions\')->get(%s)(%s))', var_export($function, true), implode(', ', $args));
                },
                function ($values, ...$args) use ($function) {
                    return $values['context']->getParameter('_functions')->get($function)(...$args);
                }
            );
        }

        return $functions;
    }

    public function get(string $function): callable
    {
        return $this->functions->get($function);
    }
}
