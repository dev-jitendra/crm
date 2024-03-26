<?php



declare(strict_types=1);

namespace Slim\Exception;

use function implode;

class HttpMethodNotAllowedException extends HttpSpecializedException
{
    
    protected array $allowedMethods = [];

    
    protected $code = 405;

    
    protected $message = 'Method not allowed.';

    protected string $title = '405 Method Not Allowed';
    protected string $description = 'The request method is not supported for the requested resource.';

    
    public function getAllowedMethods(): array
    {
        return $this->allowedMethods;
    }

    
    public function setAllowedMethods(array $methods): self
    {
        $this->allowedMethods = $methods;
        $this->message = 'Method not allowed. Must be one of: ' . implode(', ', $methods);
        return $this;
    }
}
