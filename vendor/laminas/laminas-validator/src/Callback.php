<?php

namespace Laminas\Validator;

use Exception;
use Laminas\Validator\Exception\InvalidArgumentException;

use function array_merge;
use function call_user_func_array;
use function is_callable;

class Callback extends AbstractValidator
{
    
    public const INVALID_CALLBACK = 'callbackInvalid';

    
    public const INVALID_VALUE = 'callbackValue';

    
    protected $messageTemplates = [
        self::INVALID_VALUE    => 'The input is not valid',
        self::INVALID_CALLBACK => 'An exception has been raised within the callback',
    ];

    
    protected $options = [
        'callback'        => null, 
        'callbackOptions' => [], 
    ];

    
    public function __construct($options = null)
    {
        if (is_callable($options)) {
            $options = ['callback' => $options];
        }

        parent::__construct($options);
    }

    
    public function getCallback()
    {
        return $this->options['callback'];
    }

    
    public function setCallback($callback)
    {
        if (! is_callable($callback)) {
            throw new InvalidArgumentException('Invalid callback given');
        }

        $this->options['callback'] = $callback;
        return $this;
    }

    
    public function getCallbackOptions()
    {
        return $this->options['callbackOptions'];
    }

    
    public function setCallbackOptions(mixed $options)
    {
        $this->options['callbackOptions'] = (array) $options;
        return $this;
    }

    
    public function isValid($value, $context = null)
    {
        $this->setValue($value);

        $options  = $this->getCallbackOptions();
        $callback = $this->getCallback();
        if (empty($callback)) {
            throw new InvalidArgumentException('No callback given');
        }

        $args = [$value];
        if (empty($options) && ! empty($context)) {
            $args[] = $context;
        }
        if (! empty($options) && empty($context)) {
            $args = array_merge($args, $options);
        }
        if (! empty($options) && ! empty($context)) {
            $args[] = $context;
            $args   = array_merge($args, $options);
        }

        try {
            if (! call_user_func_array($callback, $args)) {
                $this->error(self::INVALID_VALUE);
                return false;
            }
        } catch (Exception) {
            $this->error(self::INVALID_CALLBACK);
            return false;
        }

        return true;
    }
}
