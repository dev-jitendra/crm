<?php


namespace Espo\Core\Mail;


class SenderParams
{
    private ?string $fromAddress = null;
    private ?string $fromName = null;
    private ?string $replyToAddress = null;
    private ?string $replyToName = null;

    
    private $paramList = [
        'fromAddress',
        'fromName',
        'replyToAddress',
        'replyToName',
    ];

    public static function create(): self
    {
        return new self();
    }

    
    public function toArray(): array
    {
        $params = [];

        foreach ($this->paramList as $name) {
            if ($this->$name !== null) {
                $params[$name] = $this->$name;
            }
        }

        return $params;
    }

    
    public static function fromArray(array $params): self
    {
        $obj = new self();

        foreach ($obj->paramList as $name) {
            if (array_key_exists($name, $params)) {
               $obj->$name = $params[$name];
            }
        }

        return $obj;
    }

    public function getFromAddress(): ?string
    {
        return $this->fromAddress;
    }

    public function getFromName(): ?string
    {
        return $this->fromName;
    }

    public function getReplyToAddress(): ?string
    {
        return $this->replyToAddress;
    }

    public function getReplyToName(): ?string
    {
        return $this->replyToName;
    }

    public function withFromAddress(?string $fromAddress): self
    {
        $obj = clone $this;
        $obj->fromAddress = $fromAddress;

        return $obj;
    }

    public function withFromName(?string $fromName): self
    {
        $obj = clone $this;
        $obj->fromName = $fromName;

        return $obj;
    }

    public function withReplyToAddress(?string $replyToAddress): self
    {
        $obj = clone $this;
        $obj->replyToAddress = $replyToAddress;

        return $obj;
    }

    public function withReplyToName(?string $replyToName): self
    {
        $obj = clone $this;
        $obj->replyToName = $replyToName;

        return $obj;
    }
}
