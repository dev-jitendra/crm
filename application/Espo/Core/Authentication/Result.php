<?php


namespace Espo\Core\Authentication;

use Espo\Core\Authentication\Result\Data;
use Espo\Entities\User;

use stdClass;


class Result
{
    public const STATUS_SUCCESS = 'success';
    public const STATUS_SECOND_STEP_REQUIRED = 'secondStepRequired';
    public const STATUS_FAIL = 'fail';

    private ?User $user = null;
    private string $status;
    private ?string $message = null;
    private ?string $token = null;
    private ?string $view = null;
    private ?string $failReason = null;
    private ?Data $data = null;

    private function __construct(string $status, ?User $user = null, ?Data $data = null)
    {
        $this->user = $user;
        $this->status = $status;

        $this->data = $data;

        if ($data) {
            $this->message = $data->getMessage();
            $this->token = $data->getToken();
            $this->view = $data->getView();
            $this->failReason = $data->getFailReason();
        }
    }

    
    public static function success(User $user): self
    {
        return new self(self::STATUS_SUCCESS, $user);
    }

    
    public static function fail(?string $reason = null): self
    {
        $data = $reason ?
            Data::createWithFailReason($reason) :
            Data::create();

        return new self(self::STATUS_FAIL, null, $data);
    }

    
    public static function secondStepRequired(User $user, Data $data): self
    {
        return new self(self::STATUS_SECOND_STEP_REQUIRED, $user, $data);
    }

    
    public function isSuccess(): bool
    {
        return $this->status === self::STATUS_SUCCESS;
    }

    
    public function isSecondStepRequired(): bool
    {
        return $this->status === self::STATUS_SECOND_STEP_REQUIRED;
    }

    
    public function isFail(): bool
    {
        return $this->status === self::STATUS_FAIL;
    }

    
    public function getUser(): ?User
    {
        return $this->user;
    }

    
    public function getLoggedUser(): ?User
    {
        return $this->user;
    }

    
    public function getStatus(): string
    {
        return $this->status;
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

    
    public function getData(): ?stdClass
    {
        return $this->data ? $this->data->getData() : null;
    }

    
    public function getFailReason(): ?string
    {
        return $this->failReason;
    }
}
