<?php


namespace Espo\Core\Authentication\Result;

use stdClass;


class Data
{
    private ?string $message = null;
    private ?string $token = null;
    private ?string $view = null;
    private ?string $failReason = null;
    
    private array $data = [];

    private function __construct(
        ?string $message = null,
        ?string $failReason = null,
        ?string $token = null,
        ?string $view = null
    ) {
        $this->message = $message;
        $this->failReason = $failReason;
        $this->token = $token;
        $this->view = $view;
    }

    public static function create(): self
    {
        return new self();
    }

    public static function createWithFailReason(string $failReason): self
    {
        return new self(null, $failReason);
    }

    public static function createWithMessage(string $message): self
    {
        return new self($message);
    }

    public function getView(): ?string
    {
        return $this->view;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function getFailReason(): ?string
    {
        return $this->failReason;
    }

    public function getData(): stdClass
    {
        return (object) $this->data;
    }

    public function withMessage(?string $message): self
    {
        $obj = clone $this;
        $obj->message = $message;

        return $obj;
    }

    public function withFailReason(?string $failReason): self
    {
        $obj = clone $this;
        $obj->failReason = $failReason;

        return $obj;
    }

    public function withToken(?string $token): self
    {
        $obj = clone $this;
        $obj->token = $token;

        return $obj;
    }

    public function withView(?string $view): self
    {
        $obj = clone $this;
        $obj->view = $view;

        return $obj;
    }

    
    public function withDataItem(string $name, $value): self
    {
        $obj = clone $this;
        $obj->data[$name] = $value;

        return $obj;
    }
}
