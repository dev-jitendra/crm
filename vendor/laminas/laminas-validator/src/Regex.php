<?php

namespace Laminas\Validator;

use Laminas\Stdlib\ArrayUtils;
use Laminas\Stdlib\ErrorHandler;
use Traversable;

use function array_key_exists;
use function is_array;
use function is_float;
use function is_int;
use function is_string;
use function preg_match;

class Regex extends AbstractValidator
{
    public const INVALID   = 'regexInvalid';
    public const NOT_MATCH = 'regexNotMatch';
    public const ERROROUS  = 'regexErrorous';

    
    protected $messageTemplates = [
        self::INVALID   => 'Invalid type given. String, integer or float expected',
        self::NOT_MATCH => "The input does not match against pattern '%pattern%'",
        self::ERROROUS  => "There was an internal error while using the pattern '%pattern%'",
    ];

    
    protected $messageVariables = [
        'pattern' => 'pattern',
    ];

    
    protected $pattern;

    
    public function __construct($pattern)
    {
        if (is_string($pattern)) {
            $this->setPattern($pattern);
            parent::__construct([]);
            return;
        }

        if ($pattern instanceof Traversable) {
            $pattern = ArrayUtils::iteratorToArray($pattern);
        }

        if (! is_array($pattern)) {
            throw new Exception\InvalidArgumentException('Invalid options provided to constructor');
        }

        if (! array_key_exists('pattern', $pattern) || ! is_string($pattern['pattern']) || $pattern['pattern'] === '') {
            throw new Exception\InvalidArgumentException("Missing option 'pattern'");
        }

        $this->setPattern($pattern['pattern']);
        unset($pattern['pattern']);
        parent::__construct($pattern);
    }

    
    public function getPattern()
    {
        return $this->pattern;
    }

    
    public function setPattern($pattern)
    {
        ErrorHandler::start();
        $this->pattern = (string) $pattern;
        $status        = preg_match($this->pattern, 'Test');
        $error         = ErrorHandler::stop();

        if (false === $status) {
            throw new Exception\InvalidArgumentException(
                "Internal error parsing the pattern '{$this->pattern}'",
                0,
                $error
            );
        }

        return $this;
    }

    
    public function isValid($value)
    {
        if (! is_string($value) && ! is_int($value) && ! is_float($value)) {
            $this->error(self::INVALID);
            return false;
        }

        $this->setValue($value);

        ErrorHandler::start();
        $status = preg_match($this->pattern, $value);
        ErrorHandler::stop();
        if (false === $status) {
            $this->error(self::ERROROUS);
            return false;
        }

        if (! $status) {
            $this->error(self::NOT_MATCH);
            return false;
        }

        return true;
    }
}
