<?php 

namespace Laminas\Validator;

use Traversable;

use function array_shift;
use function func_get_args;
use function is_array;
use function iterator_to_array;

class Bitwise extends AbstractValidator
{
    public const OP_AND = 'and';
    public const OP_XOR = 'xor';

    public const NOT_AND        = 'notAnd';
    public const NOT_AND_STRICT = 'notAndStrict';
    public const NOT_XOR        = 'notXor';
    public const NO_OP          = 'noOp';

    
    protected $control;

    
    protected $messageTemplates = [
        self::NOT_AND        => "The input has no common bit set with '%control%'",
        self::NOT_AND_STRICT => "The input doesn't have the same bits set as '%control%'",
        self::NOT_XOR        => "The input has common bit set with '%control%'",
        self::NO_OP          => "No operator was present to compare '%control%' against",
    ];

    
    protected $messageVariables = [
        'control' => 'control',
    ];

    
    protected $operator;

    
    protected $strict = false;

    
    public function __construct($options = null)
    {
        if ($options instanceof Traversable) {
            $options = iterator_to_array($options);
        }

        if (! is_array($options)) {
            $options = func_get_args();

            $temp['control'] = array_shift($options);

            if (! empty($options)) {
                $temp['operator'] = array_shift($options);
            }

            if (! empty($options)) {
                $temp['strict'] = array_shift($options);
            }

            $options = $temp;
        }

        parent::__construct($options);
    }

    
    public function getControl()
    {
        return $this->control;
    }

    
    public function getOperator()
    {
        return $this->operator;
    }

    
    public function getStrict()
    {
        return $this->strict;
    }

    
    public function isValid($value)
    {
        $this->setValue($value);

        if (self::OP_AND === $this->operator) {
            if ($this->strict) {
                
                $result = ($this->control & $value) === $value;

                if (! $result) {
                    $this->error(self::NOT_AND_STRICT);
                }

                return $result;
            }

            
            $result = (bool) ($this->control & $value);

            if (! $result) {
                $this->error(self::NOT_AND);
            }

            return $result;
        }

        if (self::OP_XOR === $this->operator) {
            
            
            $result = ($this->control ^ $value) === ($this->control | $value);

            if (! $result) {
                $this->error(self::NOT_XOR);
            }

            return $result;
        }

        $this->error(self::NO_OP);
        return false;
    }

    
    public function setControl($control)
    {
        $this->control = (int) $control;

        return $this;
    }

    
    public function setOperator($operator)
    {
        $this->operator = $operator;

        return $this;
    }

    
    public function setStrict($strict)
    {
        $this->strict = (bool) $strict;

        return $this;
    }
}
