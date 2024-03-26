<?php



namespace Symfony\Component\HttpFoundation\Test\Constraint;

use PHPUnit\Framework\Constraint\Constraint;
use Symfony\Component\HttpFoundation\Response;

final class ResponseIsUnprocessable extends Constraint
{
    
    public function toString(): string
    {
        return 'is unprocessable';
    }

    
    protected function matches($other): bool
    {
        return Response::HTTP_UNPROCESSABLE_ENTITY === $other->getStatusCode();
    }

    
    protected function failureDescription($other): string
    {
        return 'the Response '.$this->toString();
    }

    
    protected function additionalFailureDescription($other): string
    {
        return (string) $other;
    }
}
