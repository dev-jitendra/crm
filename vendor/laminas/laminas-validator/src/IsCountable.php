<?php 

namespace Laminas\Validator;

use Traversable;

use function count;
use function is_array;
use function is_countable;
use function is_numeric;
use function sprintf;
use function ucfirst;


class IsCountable extends AbstractValidator
{
    public const NOT_COUNTABLE = 'notCountable';
    public const NOT_EQUALS    = 'notEquals';
    public const GREATER_THAN  = 'greaterThan';
    public const LESS_THAN     = 'lessThan';

    
    protected $messageTemplates = [
        self::NOT_COUNTABLE => 'The input must be an array or an instance of \\Countable',
        self::NOT_EQUALS    => "The input count must equal '%count%'",
        self::GREATER_THAN  => "The input count must be less than '%max%', inclusively",
        self::LESS_THAN     => "The input count must be greater than '%min%', inclusively",
    ];

    
    protected $messageVariables = [
        'count' => ['options' => 'count'],
        'min'   => ['options' => 'min'],
        'max'   => ['options' => 'max'],
    ];

    
    protected $options = [
        'count' => null,
        'min'   => null,
        'max'   => null,
    ];

    
    public function setOptions($options = [])
    {
        foreach (['count', 'min', 'max'] as $option) {
            if (! is_array($options) || ! isset($options[$option])) {
                continue;
            }

            $method = sprintf('set%s', ucfirst($option));
            $this->$method($options[$option]);
            unset($options[$option]);
        }

        return parent::setOptions($options);
    }

    
    public function isValid($value)
    {
        if (! is_countable($value)) {
            $this->error(self::NOT_COUNTABLE);
            return false;
        }

        $count = count($value);

        if (is_numeric($this->getCount())) {
            if ($count !== $this->getCount()) {
                $this->error(self::NOT_EQUALS);
                return false;
            }

            return true;
        }

        if (is_numeric($this->getMax()) && $count > $this->getMax()) {
            $this->error(self::GREATER_THAN);
            return false;
        }

        if (is_numeric($this->getMin()) && $count < $this->getMin()) {
            $this->error(self::LESS_THAN);
            return false;
        }

        return true;
    }

    
    public function getCount()
    {
        return $this->options['count'];
    }

    
    public function getMin()
    {
        return $this->options['min'];
    }

    
    public function getMax()
    {
        return $this->options['max'];
    }

    
    private function setCount(mixed $value)
    {
        if (isset($this->options['min']) || isset($this->options['max'])) {
            throw new Exception\InvalidArgumentException(
                'Cannot set count; conflicts with either a min or max option previously set'
            );
        }
        $this->options['count'] = $value;
    }

    
    private function setMin(mixed $value)
    {
        if (isset($this->options['count'])) {
            throw new Exception\InvalidArgumentException(
                'Cannot set count; conflicts with either a count option previously set'
            );
        }
        $this->options['min'] = $value;
    }

    
    private function setMax(mixed $value)
    {
        if (isset($this->options['count'])) {
            throw new Exception\InvalidArgumentException(
                'Cannot set count; conflicts with either a count option previously set'
            );
        }
        $this->options['max'] = $value;
    }
}
