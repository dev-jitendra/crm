<?php

namespace Laminas\Validator;

use Traversable;

use function array_key_exists;
use function array_shift;
use function func_get_args;
use function is_array;
use function iterator_to_array;

class IsInstanceOf extends AbstractValidator
{
    public const NOT_INSTANCE_OF = 'notInstanceOf';

    
    protected $messageTemplates = [
        self::NOT_INSTANCE_OF => "The input is not an instance of '%className%'",
    ];

    
    protected $messageVariables = [
        'className' => 'className',
    ];

    
    protected $className;

    
    public function __construct($options = null)
    {
        if ($options instanceof Traversable) {
            $options = iterator_to_array($options);
        }

        
        if (! is_array($options)) {
            $options = func_get_args();

            $tmpOptions              = [];
            $tmpOptions['className'] = array_shift($options);

            $options = $tmpOptions;
        }

        if (! array_key_exists('className', $options)) {
            throw new Exception\InvalidArgumentException('Missing option "className"');
        }

        parent::__construct($options);
    }

    
    public function getClassName()
    {
        return $this->className;
    }

    
    public function setClassName($className)
    {
        $this->className = $className;
        return $this;
    }

    
    public function isValid($value)
    {
        if ($value instanceof $this->className) {
            return true;
        }
        $this->error(self::NOT_INSTANCE_OF);
        return false;
    }
}
