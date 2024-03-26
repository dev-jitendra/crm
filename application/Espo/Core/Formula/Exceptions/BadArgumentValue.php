<?php


namespace Espo\Core\Formula\Exceptions;


class BadArgumentValue extends Error
{
    private ?int $position = null;

    
    public static function create(int $position): self
    {
        $obj = new self();
        $obj->position = $position;

        return $obj;
    }

    public function getLogMessage(): string
    {
        $position = (string) ($this->position ?? '?');

        return "Bad argument value on position {$position}.";
    }
}
