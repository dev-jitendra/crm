<?php


namespace Espo\Tools\LeadCapture;

class ConfirmResult
{
    public const STATUS_SUCCESS = 'success';
    public const STATUS_EXPIRED = 'expired';

    public function __construct(
        private string $status,
        private ?string $message,
        private ?string $leadCaptureId = null,
        private ?string $leadCaptureName = null
    ) {}

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function getLeadCaptureId(): ?string
    {
        return $this->leadCaptureId;
    }

    public function getLeadCaptureName(): ?string
    {
        return $this->leadCaptureName;
    }
}
