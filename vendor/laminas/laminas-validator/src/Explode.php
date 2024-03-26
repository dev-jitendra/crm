<?php

namespace Laminas\Validator;

use Laminas\ServiceManager\ServiceManager;
use Laminas\Stdlib\ArrayUtils;
use Traversable;

use function explode;
use function is_array;
use function is_string;
use function sprintf;


class Explode extends AbstractValidator implements ValidatorPluginManagerAwareInterface
{
    public const INVALID = 'explodeInvalid';

    
    protected $pluginManager;

    
    protected $messageTemplates = [
        self::INVALID => 'Invalid type given',
    ];

    
    protected $messageVariables = [];

    
    protected $valueDelimiter = ',';

    
    protected $validator;

    
    protected $breakOnFirstFailure = false;

    
    public function setValueDelimiter($delimiter)
    {
        $this->valueDelimiter = $delimiter;
        return $this;
    }

    
    public function getValueDelimiter()
    {
        return $this->valueDelimiter;
    }

    
    public function setValidatorPluginManager(ValidatorPluginManager $pluginManager)
    {
        $this->pluginManager = $pluginManager;
    }

    
    public function getValidatorPluginManager()
    {
        if (! $this->pluginManager) {
            $this->pluginManager = new ValidatorPluginManager(new ServiceManager());
        }

        return $this->pluginManager;
    }

    
    public function setValidator($validator)
    {
        if (is_array($validator)) {
            if (! isset($validator['name'])) {
                throw new Exception\RuntimeException(
                    'Invalid validator specification provided; does not include "name" key'
                );
            }
            $name    = $validator['name'];
            $options = $validator['options'] ?? [];
            
            $validator = $this->getValidatorPluginManager()->get($name, $options);
        }

        if (! $validator instanceof ValidatorInterface) {
            throw new Exception\RuntimeException(
                'Invalid validator given'
            );
        }

        $this->validator = $validator;
        return $this;
    }

    
    public function getValidator()
    {
        return $this->validator;
    }

    
    public function setBreakOnFirstFailure($break)
    {
        $this->breakOnFirstFailure = (bool) $break;
        return $this;
    }

    
    public function isBreakOnFirstFailure()
    {
        return $this->breakOnFirstFailure;
    }

    
    public function isValid($value, $context = null)
    {
        $this->setValue($value);

        if ($value instanceof Traversable) {
            $value = ArrayUtils::iteratorToArray($value);
        }

        if (is_array($value)) {
            $values = $value;
        } elseif (is_string($value)) {
            $delimiter = $this->getValueDelimiter();
            
            
            
            
            $values = null !== $delimiter
                      ? explode($this->valueDelimiter, $value)
                      : [$value];
        } else {
            $values = [$value];
        }

        $validator = $this->getValidator();

        if (! $validator) {
            throw new Exception\RuntimeException(sprintf(
                '%s expects a validator to be set; none given',
                __METHOD__
            ));
        }

        foreach ($values as $value) {
            if (! $validator->isValid($value, $context)) {
                $this->abstractOptions['messages'][] = $validator->getMessages();

                if ($this->isBreakOnFirstFailure()) {
                    return false;
                }
            }
        }

        return ! $this->abstractOptions['messages'];
    }
}
