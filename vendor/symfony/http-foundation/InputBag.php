<?php



namespace Symfony\Component\HttpFoundation;

use Symfony\Component\HttpFoundation\Exception\BadRequestException;


final class InputBag extends ParameterBag
{
    
    public function get(string $key, mixed $default = null): string|int|float|bool|null
    {
        if (null !== $default && !\is_scalar($default) && !$default instanceof \Stringable) {
            throw new \InvalidArgumentException(sprintf('Expected a scalar value as a 2nd argument to "%s()", "%s" given.', __METHOD__, get_debug_type($default)));
        }

        $value = parent::get($key, $this);

        if (null !== $value && $this !== $value && !\is_scalar($value) && !$value instanceof \Stringable) {
            throw new BadRequestException(sprintf('Input value "%s" contains a non-scalar value.', $key));
        }

        return $this === $value ? $default : $value;
    }

    
    public function replace(array $inputs = [])
    {
        $this->parameters = [];
        $this->add($inputs);
    }

    
    public function add(array $inputs = [])
    {
        foreach ($inputs as $input => $value) {
            $this->set($input, $value);
        }
    }

    
    public function set(string $key, mixed $value)
    {
        if (null !== $value && !\is_scalar($value) && !\is_array($value) && !$value instanceof \Stringable) {
            throw new \InvalidArgumentException(sprintf('Expected a scalar, or an array as a 2nd argument to "%s()", "%s" given.', __METHOD__, get_debug_type($value)));
        }

        $this->parameters[$key] = $value;
    }

    
    public function filter(string $key, mixed $default = null, int $filter = \FILTER_DEFAULT, mixed $options = []): mixed
    {
        $value = $this->has($key) ? $this->all()[$key] : $default;

        
        if (!\is_array($options) && $options) {
            $options = ['flags' => $options];
        }

        if (\is_array($value) && !(($options['flags'] ?? 0) & (\FILTER_REQUIRE_ARRAY | \FILTER_FORCE_ARRAY))) {
            throw new BadRequestException(sprintf('Input value "%s" contains an array, but "FILTER_REQUIRE_ARRAY" or "FILTER_FORCE_ARRAY" flags were not set.', $key));
        }

        if ((\FILTER_CALLBACK & $filter) && !(($options['options'] ?? null) instanceof \Closure)) {
            throw new \InvalidArgumentException(sprintf('A Closure must be passed to "%s()" when FILTER_CALLBACK is used, "%s" given.', __METHOD__, get_debug_type($options['options'] ?? null)));
        }

        return filter_var($value, $filter, $options);
    }
}
