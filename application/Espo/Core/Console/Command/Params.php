<?php


namespace Espo\Core\Console\Command;

use Espo\Core\Utils\Util;


class Params
{
    
    private $options;
    
    private $flagList;
    
    private $argumentList;

    
    public function __construct(?array $options, ?array $flagList, ?array $argumentList)
    {
        $this->options = $options ?? [];
        $this->flagList = $flagList ?? [];
        $this->argumentList = $argumentList ?? [];
    }

    
    public function getOptions(): array
    {
        return $this->options;
    }

    
    public function getFlagList(): array
    {
        return $this->flagList;
    }

    
    public function getArgumentList(): array
    {
        return $this->argumentList;
    }

    
    public function hasOption(string $name): bool
    {
        return array_key_exists($name, $this->options);
    }

    
    public function getOption(string $name): ?string
    {
        return $this->options[$name] ?? null;
    }

    
    public function hasFlag(string $name): bool
    {
        return in_array($name, $this->flagList);
    }

    
    public function getArgument(int $index): ?string
    {
        return $this->argumentList[$index] ?? null;
    }

    
    public static function fromArgs(array $args): self
    {
        $argumentList = [];
        $options = [];
        $flagList = [];

        foreach ($args as $i => $item) {
            if (str_starts_with($item, '--') && strpos($item, '=') > 2) {
                [$name, $value] = explode('=', substr($item, 2));

                $name = Util::hyphenToCamelCase($name);

                $options[$name] = $value;
            }
            else if (str_starts_with($item, '--')) {
                $flagList[] = Util::hyphenToCamelCase(substr($item, 2));
            }
            else if (str_starts_with($item, '-')) {
                $flagList[] = substr($item, 1);
            }
            else if ($i > 0) {
                $argumentList[] = $item;
            }
        }

        return new self($options, $flagList, $argumentList);
    }
}
