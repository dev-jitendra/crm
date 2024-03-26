<?php


namespace Espo\Core\Formula\Exceptions;


class BadArgumentType extends Error
{
    private ?int $position = null;
    private ?string $type = null;

    
    public static function create(int $position, string $type): self
    {
        $obj = new self();
        $obj->position = $position;
        $obj->type = $type;

        return $obj;
    }

    public function getLogMessage(): string
    {
        $position = (string) ($this->position ?? '?');
        $type = $this->type ?? '?';

        return "Bad argument type on position {$position}, must be {$type}.";
    }
}
