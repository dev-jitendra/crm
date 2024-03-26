<?php



namespace Symfony\Component\Routing\Exception;


class MethodNotAllowedException extends \RuntimeException implements ExceptionInterface
{
    protected $allowedMethods = [];

    
    public function __construct(array $allowedMethods, string $message = '', int $code = 0, \Throwable $previous = null)
    {
        $this->allowedMethods = array_map('strtoupper', $allowedMethods);

        parent::__construct($message, $code, $previous);
    }

    
    public function getAllowedMethods(): array
    {
        return $this->allowedMethods;
    }
}
