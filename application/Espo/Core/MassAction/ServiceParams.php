<?php


namespace Espo\Core\MassAction;


class ServiceParams
{
    private bool $isIdle = false;

    private function __construct(private Params $params)
    {}

    public static function create(Params $params): self
    {
        return new self($params);
    }

    public function getParams(): Params
    {
        return $this->params;
    }

    public function isIdle(): bool
    {
        return $this->isIdle;
    }

    public function withIsIdle(bool $isIdle = true): self
    {
        $obj = clone $this;
        $obj->isIdle = $isIdle;

        return $obj;
    }
}
