<?php


namespace Espo\Core\Exceptions;

use Espo\Core\Exceptions\Error\Body;
use Espo\Core\Utils\Log;
use Throwable;
use Exception;


class BadRequest extends Exception implements HasBody, HasLogLevel
{
    
    protected $code = 400;
    protected ?string $body = null;

    final public function __construct(string $message = '', int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    
    public static function createWithBody(string $reason, string|Body $body): self
    {
        if ($body instanceof Body) {
            $body = $body->encode();
        }

        $exception = new static($reason);
        $exception->body = $body;

        return $exception;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function getLogLevel(): string
    {
        return Log::LEVEL_WARNING;
    }
}
