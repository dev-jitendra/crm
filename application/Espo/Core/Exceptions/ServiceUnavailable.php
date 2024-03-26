<?php


namespace Espo\Core\Exceptions;

use Espo\Core\Exceptions\Error\Body;
use Throwable;

class ServiceUnavailable extends \Exception implements HasBody
{
    
    protected $code = 503;
    private ?string $body = null;

    final public function __construct(string $message = '', int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    
    public static function createWithBody(string $message, string|Body $body): self
    {
        if ($body instanceof Body) {
            $body = $body->encode();
        }

        $exception = new static($message);
        $exception->body = $body;

        return $exception;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }
}
