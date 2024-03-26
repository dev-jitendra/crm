<?php


namespace Espo\Tools\Formula;

use Espo\Core\Formula\Exceptions\SyntaxError;
use Espo\Core\Formula\Exceptions\Error;

use stdClass;

class SyntaxCheckResult
{
    private bool $isSuccess = false;

    private ?string $message = null;

    private ?Error $exception = null;

    private function __construct(bool $isSuccess)
    {
        $this->isSuccess = $isSuccess;
    }

    public static function createSuccess(): self
    {
        return new self(true);
    }

    public static function createError(SyntaxError $exception): self
    {
        $obj = new self(false);

        $obj->message = $exception->getShortMessage();
        $obj->exception = $exception;

        return $obj;
    }

    public function isSuccess(): bool
    {
        return $this->isSuccess;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function getException(): ?Error
    {
        return $this->exception;
    }

    public function toStdClass(): stdClass
    {
        $data = (object) [];

        $data->isSuccess = $this->isSuccess();

        if (!$this->isSuccess) {
            $data->message = $this->message;
        }

        return $data;
    }
}
