<?php



namespace Symfony\Component\HttpFoundation;

use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;


class ExpressionRequestMatcher extends RequestMatcher
{
    private $language;
    private $expression;

    public function setExpression(ExpressionLanguage $language, Expression|string $expression)
    {
        $this->language = $language;
        $this->expression = $expression;
    }

    public function matches(Request $request): bool
    {
        if (!isset($this->language)) {
            throw new \LogicException('Unable to match the request as the expression language is not available.');
        }

        return $this->language->evaluate($this->expression, [
            'request' => $request,
            'method' => $request->getMethod(),
            'path' => rawurldecode($request->getPathInfo()),
            'host' => $request->getHost(),
            'ip' => $request->getClientIp(),
            'attributes' => $request->attributes->all(),
        ]) && parent::matches($request);
    }
}
