<?php


namespace Espo\Core\Authentication\Logout;


class Result
{
    private ?string $redirectUrl = null;

    private function __construct() {}

    public static function create(): self
    {
        return new self();
    }

    public function withRedirectUrl(?string $redirectUrl): self
    {
        $obj = clone $this;
        $obj->redirectUrl = $redirectUrl;

        return $obj;
    }

    public function getRedirectUrl(): ?string
    {
        return $this->redirectUrl;
    }
}
