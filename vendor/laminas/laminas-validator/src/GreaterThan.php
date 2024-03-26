<?php

namespace Laminas\Validator;

use Laminas\Stdlib\ArrayUtils;
use Traversable;

use function array_key_exists;
use function array_shift;
use function func_get_args;
use function is_array;

class GreaterThan extends AbstractValidator
{
    public const NOT_GREATER           = 'notGreaterThan';
    public const NOT_GREATER_INCLUSIVE = 'notGreaterThanInclusive';

    
    protected $messageTemplates = [
        self::NOT_GREATER           => "The input is not greater than '%min%'",
        self::NOT_GREATER_INCLUSIVE => "The input is not greater than or equal to '%min%'",
    ];

    
    protected $messageVariables = [
        'min' => 'min',
    ];

    
    protected $min;

    
    protected $inclusive;

    
    public function __construct($options = null)
    {
        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        }
        if (! is_array($options)) {
            $options     = func_get_args();
            $temp['min'] = array_shift($options);

            if (! empty($options)) {
                $temp['inclusive'] = array_shift($options);
            }

            $options = $temp;
        }

        if (! array_key_exists('min', $options)) {
            throw new Exception\InvalidArgumentException("Missing option 'min'");
        }

        if (! array_key_exists('inclusive', $options)) {
            $options['inclusive'] = false;
        }

        $this->setMin($options['min'])
             ->setInclusive($options['inclusive']);

        parent::__construct($options);
    }

    
    public function getMin()
    {
        return $this->min;
    }

    
    public function setMin(mixed $min)
    {
        $this->min = $min;
        return $this;
    }

    
    public function getInclusive()
    {
        return $this->inclusive;
    }

    
    public function setInclusive($inclusive)
    {
        $this->inclusive = $inclusive;
        return $this;
    }

    
    public function isValid($value)
    {
        $this->setValue($value);

        if ($this->inclusive) {
            if ($this->min > $value) {
                $this->error(self::NOT_GREATER_INCLUSIVE);
                return false;
            }
        } else {
            if ($this->min >= $value) {
                $this->error(self::NOT_GREATER);
                return false;
            }
        }

        return true;
    }
}
