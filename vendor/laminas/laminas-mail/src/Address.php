<?php

namespace Laminas\Mail;

use Laminas\Validator\EmailAddress as EmailAddressValidator;
use Laminas\Validator\Hostname;

use function array_shift;
use function is_string;
use function preg_match;
use function sprintf;
use function trim;

class Address implements Address\AddressInterface
{
    
    protected $comment;
    
    protected $email;
    
    protected $name;

    
    public static function fromString($address, $comment = null)
    {
        if (! preg_match('/^((?P<name>.*)<(?P<namedEmail>[^>]+)>|(?P<email>.+))$/', $address, $matches)) {
            throw new Exception\InvalidArgumentException('Invalid address format');
        }

        $name = null;
        if (isset($matches['name'])) {
            $name = trim($matches['name']);
        }
        if (empty($name)) {
            $name = null;
        }

        if (isset($matches['namedEmail'])) {
            $email = $matches['namedEmail'];
        }
        if (isset($matches['email'])) {
            $email = $matches['email'];
        }
        $email = trim($email);
        
        $email = trim($email, '\'');

        return new static($email, $name, $comment);
    }

    
    public function __construct($email, $name = null, $comment = null)
    {
        $emailAddressValidator = new EmailAddressValidator(Hostname::ALLOW_DNS | Hostname::ALLOW_LOCAL);
        if (! is_string($email) || empty($email)) {
            throw new Exception\InvalidArgumentException('Email must be a valid email address');
        }

        if (preg_match("/[\r\n]/", $email)) {
            throw new Exception\InvalidArgumentException('CRLF injection detected');
        }

        if (! $emailAddressValidator->isValid($email)) {
            $invalidMessages = $emailAddressValidator->getMessages();
            throw new Exception\InvalidArgumentException(array_shift($invalidMessages));
        }

        if (null !== $name) {
            if (! is_string($name)) {
                throw new Exception\InvalidArgumentException('Name must be a string');
            }

            if (preg_match("/[\r\n]/", $name)) {
                throw new Exception\InvalidArgumentException('CRLF injection detected');
            }

            $this->name = $name;
        }

        $this->email = $email;

        if (null !== $comment) {
            $this->comment = $comment;
        }
    }

    
    public function getEmail()
    {
        return $this->email;
    }

    
    public function getName()
    {
        return $this->name;
    }

    
    public function getComment()
    {
        return $this->comment;
    }

    
    public function toString()
    {
        $string = sprintf('<%s>', $this->getEmail());
        $name   = $this->constructName();
        if (null === $name) {
            return $string;
        }

        return sprintf('%s %s', $name, $string);
    }

    
    private function constructName()
    {
        $name    = $this->getName();
        $comment = $this->getComment();

        if ($comment === null || $comment === '') {
            return $name;
        }

        $string = sprintf('%s (%s)', $name, $comment);
        return trim($string);
    }
}
