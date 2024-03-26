<?php


namespace Espo\Tools\Formula;

use Espo\Core\Formula\Exceptions\SyntaxError;
use Espo\Core\Formula\Exceptions\Error;

use stdClass;

class RunResult
{
    private bool $isSuccess = false;
    private ?string $output = null;
    private ?string $message = null;
    private ?Error $exception = null;

    private function __construct(bool $isSuccess)
    {
        $this->isSuccess = $isSuccess;
    }

    public static function createSuccess(?string $output): self
    {
        $obj = new self(true);
        $obj->output = $output;

        return $obj;
    }

    public static function createError(Error $exception, ?string $output): self
    {
        $obj = new self(false);

        $obj->message = $exception->getMessage();
        $obj->exception = $exception;
        $obj->output = $output;

        return $obj;
    }

    public static function createSyntaxError(SyntaxError $exception): self
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

    public function getOutput(): ?string
    {
        return $this->output;
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
            $data->isSyntaxError = $this->exception instanceof SyntaxError;
        }

        $data->output = $this->output;

        return $data;
    }
}
