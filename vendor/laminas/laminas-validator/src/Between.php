<?php

namespace Laminas\Validator;

use Laminas\Stdlib\ArrayUtils;
use Traversable;

use function array_key_exists;
use function array_shift;
use function func_get_args;
use function is_array;
use function is_numeric;
use function is_string;

use const PHP_INT_MAX;

class Between extends AbstractValidator
{
    public const NOT_BETWEEN        = 'notBetween';
    public const NOT_BETWEEN_STRICT = 'notBetweenStrict';
    public const VALUE_NOT_NUMERIC  = 'valueNotNumeric';
    public const VALUE_NOT_STRING   = 'valueNotString';

    
    private ?bool $numeric = null;

    
    protected $messageTemplates = [
        self::NOT_BETWEEN        => "The input is not between '%min%' and '%max%', inclusively",
        self::NOT_BETWEEN_STRICT => "The input is not strictly between '%min%' and '%max%'",
        self::VALUE_NOT_NUMERIC  => "The min ('%min%') and max ('%max%') values are numeric, but the input is not",
        self::VALUE_NOT_STRING   => "The min ('%min%') and max ('%max%') values are non-numeric strings, "
            . 'but the input is not a string',
    ];

    
    protected $messageVariables = [
        'min' => ['options' => 'min'],
        'max' => ['options' => 'max'],
    ];

    
    protected $options = [
        'inclusive' => true, 
        'min'       => 0,
        'max'       => PHP_INT_MAX,
    ];

    
    public function __construct($options = null)
    {
        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        }
        if (! is_array($options)) {
            $temp = [];
            
            $options     = func_get_args();
            $temp['min'] = array_shift($options);
            if (! empty($options)) {
                $temp['max'] = array_shift($options);
            }

            if (! empty($options)) {
                $temp['inclusive'] = array_shift($options);
            }

            $options = $temp;
        }

        if (! array_key_exists('min', $options) || ! array_key_exists('max', $options)) {
            throw new Exception\InvalidArgumentException("Missing option: 'min' and 'max' have to be given");
        }

        if (
            (isset($options['min']) && is_numeric($options['min']))
            && (isset($options['max']) && is_numeric($options['max']))
        ) {
            $this->numeric = true;
        } elseif (
            (isset($options['min']) && is_string($options['min']))
            && (isset($options['max']) && is_string($options['max']))
        ) {
            $this->numeric = false;
        } else {
            throw new Exception\InvalidArgumentException(
                "Invalid options: 'min' and 'max' should be of the same scalar type"
            );
        }

        parent::__construct($options);
    }

    
    public function getMin()
    {
        return $this->options['min'];
    }

    
    public function setMin(mixed $min)
    {
        $this->options['min'] = $min;
        return $this;
    }

    
    public function getMax()
    {
        return $this->options['max'];
    }

    
    public function setMax(mixed $max)
    {
        $this->options['max'] = $max;
        return $this;
    }

    
    public function getInclusive()
    {
        return $this->options['inclusive'];
    }

    
    public function setInclusive($inclusive)
    {
        $this->options['inclusive'] = $inclusive;
        return $this;
    }

    
    public function isValid($value)
    {
        $this->setValue($value);

        if ($this->numeric && ! is_numeric($value)) {
            $this->error(self::VALUE_NOT_NUMERIC);
            return false;
        }
        if (! $this->numeric && ! is_string($value)) {
            $this->error(self::VALUE_NOT_STRING);
            return false;
        }

        if ($this->getInclusive()) {
            if ($this->getMin() > $value || $value > $this->getMax()) {
                $this->error(self::NOT_BETWEEN);
                return false;
            }
        } else {
            if ($this->getMin() >= $value || $value >= $this->getMax()) {
                $this->error(self::NOT_BETWEEN_STRICT);
                return false;
            }
        }

        return true;
    }
}
