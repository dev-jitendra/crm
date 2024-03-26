<?php


namespace Espo\Tools\EmailTemplate;


class Params
{
    private bool $applyAcl = false;
    private bool $copyAttachments = false;

    public function applyAcl(): bool
    {
        return $this->applyAcl;
    }

    public function copyAttachments(): bool
    {
        return $this->copyAttachments;
    }

    
    public function withApplyAcl(bool $applyAcl = true): self
    {
        $obj = clone $this;
        $obj->applyAcl = $applyAcl;

        return $obj;
    }

    
    public function withCopyAttachments(bool $copyAttachments = true): self
    {
        $obj = clone $this;
        $obj->copyAttachments = $copyAttachments;

        return $obj;
    }

    public static function create(): self
    {
        return new self();
    }
}
