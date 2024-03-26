<?php



namespace Symfony\Component\HttpFoundation\Test\Constraint;

use PHPUnit\Framework\Constraint\Constraint;
use Symfony\Component\HttpFoundation\Response;

final class ResponseIsRedirected extends Constraint
{
    
    public function toString(): string
    {
        return 'is redirected';
    }

    
    protected function matches($response): bool
    {
        return $response->isRedirect();
    }

    
    protected function failureDescription($response): string
    {
        return 'the Response '.$this->toString();
    }

    
    protected function additionalFailureDescription($response): string
    {
        return (string) $response;
    }
}
