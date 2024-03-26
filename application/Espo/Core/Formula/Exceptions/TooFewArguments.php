<?php


namespace Espo\Core\Formula\Exceptions;


class TooFewArguments extends Error
{
    private ?int $number = null;

    
    public static function create(int $number): self
    {
        $obj = new self();
        $obj->number = $number;

        return $obj;
    }

    public function getLogMessage(): string
    {
        $number = (string) ($this->number ?? '?');

        return "Too few arguments passed, must be {$number}.";
    }
}
