<?php

namespace Laminas\Validator;

use Laminas\Stdlib\StringUtils;
use Laminas\Stdlib\StringWrapper\StringWrapperInterface as StringWrapper;
use Laminas\Stdlib\StringWrapper\StringWrapperInterface;
use Traversable;

use function array_shift;
use function func_get_args;
use function is_array;
use function is_string;
use function max;

class StringLength extends AbstractValidator
{
    public const INVALID   = 'stringLengthInvalid';
    public const TOO_SHORT = 'stringLengthTooShort';
    public const TOO_LONG  = 'stringLengthTooLong';

    
    protected $messageTemplates = [
        self::INVALID   => 'Invalid type given. String expected',
        self::TOO_SHORT => 'The input is less than %min% characters long',
        self::TOO_LONG  => 'The input is more than %max% characters long',
    ];

    
    protected $messageVariables = [
        'min'    => ['options' => 'min'],
        'max'    => ['options' => 'max'],
        'length' => ['options' => 'length'],
    ];

    
    protected $options = [
        'min'      => 0, 
        'max'      => null, 
        'encoding' => 'UTF-8', 
        'length'   => 0, 
    ];

    
    protected $stringWrapper;

    
    public function __construct($options = [])
    {
        if (! is_array($options)) {
            $options     = func_get_args();
            $temp['min'] = array_shift($options);
            if (! empty($options)) {
                $temp['max'] = array_shift($options);
            }

            if (! empty($options)) {
                $temp['encoding'] = array_shift($options);
            }

            $options = $temp;
        }

        parent::__construct($options);
    }

    
    public function getMin()
    {
        return $this->options['min'];
    }

    
    public function setMin($min)
    {
        if (null !== $this->getMax() && $min > $this->getMax()) {
            throw new Exception\InvalidArgumentException(
                "The minimum must be less than or equal to the maximum length, but {$min} > {$this->getMax()}"
            );
        }

        $this->options['min'] = max(0, (int) $min);
        return $this;
    }

    
    public function getMax()
    {
        return $this->options['max'];
    }

    
    public function setMax($max)
    {
        if (null === $max) {
            $this->options['max'] = null;
        } elseif ($max < $this->getMin()) {
            throw new Exception\InvalidArgumentException(
                "The maximum must be greater than or equal to the minimum length, but {$max} < {$this->getMin()}"
            );
        } else {
            $this->options['max'] = (int) $max;
        }

        return $this;
    }

    
    public function getStringWrapper()
    {
        if (! $this->stringWrapper) {
            $this->stringWrapper = StringUtils::getWrapper($this->getEncoding());
        }
        return $this->stringWrapper;
    }

    
    public function setStringWrapper(StringWrapper $stringWrapper)
    {
        $stringWrapper->setEncoding($this->getEncoding());
        $this->stringWrapper = $stringWrapper;
    }

    
    public function getEncoding()
    {
        return $this->options['encoding'];
    }

    
    public function setEncoding($encoding)
    {
        $this->stringWrapper       = StringUtils::getWrapper($encoding);
        $this->options['encoding'] = $encoding;
        return $this;
    }

    
    private function getLength()
    {
        return $this->options['length'];
    }

    
    private function setLength($length)
    {
        $this->options['length'] = (int) $length;
        return $this;
    }

    
    public function isValid($value)
    {
        if (! is_string($value)) {
            $this->error(self::INVALID);
            return false;
        }

        $this->setValue($value);

        $this->setLength($this->getStringWrapper()->strlen($value));
        if ($this->getLength() < $this->getMin()) {
            $this->error(self::TOO_SHORT);
        }

        if (null !== $this->getMax() && $this->getMax() < $this->getLength()) {
            $this->error(self::TOO_LONG);
        }

        if ($this->getMessages()) {
            return false;
        }

        return true;
    }
}
