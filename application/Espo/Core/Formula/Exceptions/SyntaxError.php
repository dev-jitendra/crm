<?php


namespace Espo\Core\Formula\Exceptions;

use Throwable;

class SyntaxError extends Error
{
    
    private $shortMessage = null;

    final public function __construct(string $message = '', int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function create(string $message, ?string $shortMessage = null): self
    {
        $obj = new static($message);
        $obj->shortMessage = $shortMessage;

        return $obj;
    }

    public function getShortMessage(): ?string
    {
        return $this->shortMessage ?? $this->getMessage();
    }
}
