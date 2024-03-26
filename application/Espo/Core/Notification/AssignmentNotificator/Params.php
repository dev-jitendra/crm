<?php


namespace Espo\Core\Notification\AssignmentNotificator;


class Params
{
    
    private $options = [];

    public function hasOption(string $option): bool
    {
        return array_key_exists($option, $this->options);
    }

    
    public function getOption(string $option)
    {
        return $this->options[$option] ?? null;
    }

    
    public function getRawOptions(): array
    {
        return $this->options;
    }

    
    public function withRawOptions(array $options): self
    {
        $obj = clone $this;

        $obj->options = $options;

        return $obj;
    }

    public static function create(): self
    {
        return new self();
    }
}
