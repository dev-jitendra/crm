<?php



namespace Symfony\Component\HttpFoundation\Test\Constraint;

use PHPUnit\Framework\Constraint\Constraint;
use Symfony\Component\HttpFoundation\Response;

final class ResponseIsSuccessful extends Constraint
{
    
    public function toString(): string
    {
        return 'is successful';
    }

    
    protected function matches($response): bool
    {
        return $response->isSuccessful();
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
