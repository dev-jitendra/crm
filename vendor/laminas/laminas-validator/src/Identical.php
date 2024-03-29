<?php

namespace Laminas\Validator;

use ArrayAccess;
use Laminas\Stdlib\ArrayUtils;
use Traversable;

use function array_key_exists;
use function get_debug_type;
use function is_array;
use function is_int;
use function is_string;
use function key;
use function sprintf;
use function var_export;

class Identical extends AbstractValidator
{
    
    public const NOT_SAME      = 'notSame';
    public const MISSING_TOKEN = 'missingToken';

    
    protected $messageTemplates = [
        self::NOT_SAME      => 'The two given tokens do not match',
        self::MISSING_TOKEN => 'No token was provided to match against',
    ];

    
    protected $messageVariables = [
        'token' => 'tokenString',
    ];

    
    protected $tokenString;

    
    protected $token;

    
    protected $strict = true;

    
    protected $literal = false;

    
    public function __construct($token = null)
    {
        if ($token instanceof Traversable) {
            $token = ArrayUtils::iteratorToArray($token);
        }

        if (is_array($token) && array_key_exists('token', $token)) {
            if (array_key_exists('strict', $token)) {
                $this->setStrict($token['strict']);
            }

            if (array_key_exists('literal', $token)) {
                $this->setLiteral($token['literal']);
            }

            $this->setToken($token['token']);
        } elseif (null !== $token) {
            $this->setToken($token);
        }

        parent::__construct(is_array($token) ? $token : null);
    }

    
    public function getToken()
    {
        return $this->token;
    }

    
    public function setToken(mixed $token)
    {
        $this->tokenString = is_array($token) ? var_export($token, true) : (string) $token;
        $this->token       = $token;
        return $this;
    }

    
    public function getStrict()
    {
        return $this->strict;
    }

    
    public function setStrict($strict)
    {
        $this->strict = (bool) $strict;
        return $this;
    }

    
    public function getLiteral()
    {
        return $this->literal;
    }

    
    public function setLiteral($literal)
    {
        $this->literal = (bool) $literal;
        return $this;
    }

    
    public function isValid($value, $context = null)
    {
        $this->setValue($value);

        $token = $this->getToken();

        if (! $this->getLiteral() && $context !== null) {
            if (! is_array($context) && ! $context instanceof ArrayAccess) {
                throw new Exception\InvalidArgumentException(sprintf(
                    'Context passed to %s must be array, ArrayObject or null; received "%s"',
                    __METHOD__,
                    get_debug_type($context)
                ));
            }

            if (is_array($token)) {
                while (is_array($token)) {
                    $key = key($token);
                    if (! isset($context[$key])) {
                        break;
                    }
                    $context = $context[$key];
                    $token   = $token[$key];
                }
            }

            
            
            if (
                is_array($token)
                || (! is_int($token) && ! is_string($token))
                || ! isset($context[$token])
            ) {
                $token = $this->getToken();
            } else {
                $token = $context[$token];
            }
        }

        if ($token === null) {
            $this->error(self::MISSING_TOKEN);
            return false;
        }

        $strict = $this->getStrict();
        if (
            ($strict && ($value !== $token))
            
            || (! $strict && ($value != $token))
        ) {
            $this->error(self::NOT_SAME);
            return false;
        }

        return true;
    }
}
