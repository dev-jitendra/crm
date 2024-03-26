<?php


namespace Espo\Core\Htmlizer\Helper;

use stdClass;
use Closure;

class Data
{
    
    private $argumentList;

    private stdClass $options;

    private int $blockParams;

    
    private $context;

    private string $name;

    
    private $rootContext;

    
    private $func = null;

    
    private $inverseFunc = null;

    
    public function __construct(
        string $name,
        array $argumentList,
        stdClass $options,
        array $context,
        array $rootContext,
        int $blockParams,
        ?Closure $func,
        ?Closure $inverseFunc
    ) {
        $this->name = $name;
        $this->argumentList = $argumentList;
        $this->options = $options;
        $this->context = $context;
        $this->rootContext = $rootContext;
        $this->blockParams = $blockParams;
        $this->func = $func;
        $this->inverseFunc = $inverseFunc;
    }

    public function getName(): string
    {
        return $this->name;
    }

    
    public function getContext(): array
    {
        return $this->context;
    }

    
    public function getRootContext(): array
    {
        return $this->rootContext;
    }

    public function getOptions(): stdClass
    {
        return $this->options;
    }

    
    public function getArgumentList(): array
    {
        return $this->argumentList;
    }

    public function hasOption(string $name): bool
    {
        return property_exists($this->options, $name);
    }

    
    public function getOption(string $name)
    {
        return $this->options->$name ?? null;
    }

    public function getFunction(): ?Closure
    {
        return $this->func;
    }

    public function getInverseFunction(): ?Closure
    {
        return $this->inverseFunc;
    }
}
