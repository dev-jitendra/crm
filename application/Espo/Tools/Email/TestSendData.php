<?php


namespace Espo\Tools\Email;

class TestSendData
{
    private string $emailAddress;
    private ?string $type;
    private ?string $id;
    private ?string $userId;

    public function __construct(
        string $emailAddress,
        ?string $type,
        ?string $id,
        ?string $userId
    ) {
        $this->emailAddress = $emailAddress;
        $this->type = $type;
        $this->id = $id;
        $this->userId = $userId;
    }

    public function getEmailAddress(): string
    {
        return $this->emailAddress;
    }

    public function getType(): ?string
    {
        return $this->type;
    }


    public function getId(): ?string
    {
        return $this->id;
    }

    public function getUserId(): ?string
    {
        return $this->userId;
    }
}
