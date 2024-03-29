<?php

namespace Laminas\Validator\File;

use Laminas\Validator\AbstractValidator;
use Laminas\Validator\Exception;

use function array_key_exists;
use function array_unique;
use function array_values;
use function func_get_arg;
use function func_num_args;
use function get_debug_type;
use function hash_algos;
use function hash_file;
use function in_array;
use function is_array;
use function is_readable;
use function is_scalar;
use function is_string;
use function sprintf;


class Hash extends AbstractValidator
{
    use FileInformationTrait;

    
    public const DOES_NOT_MATCH = 'fileHashDoesNotMatch';
    public const NOT_DETECTED   = 'fileHashHashNotDetected';
    public const NOT_FOUND      = 'fileHashNotFound';

    
    protected $messageTemplates = [
        self::DOES_NOT_MATCH => 'File does not match the given hashes',
        self::NOT_DETECTED   => 'A hash could not be evaluated for the given file',
        self::NOT_FOUND      => 'File is not readable or does not exist',
    ];

    
    protected $options = [
        'algorithm' => 'crc32',
        'hash'      => null,
    ];

    
    public function __construct($options = null)
    {
        if (
            is_scalar($options) ||
            (is_array($options) && ! array_key_exists('hash', $options))
        ) {
            $options = ['hash' => $options];
        }

        if (1 < func_num_args()) {
            $options['algorithm'] = func_get_arg(1);
        }

        parent::__construct($options);
    }

    
    public function getHash()
    {
        return $this->options['hash'];
    }

    
    public function setHash($options)
    {
        $this->options['hash'] = null;
        $this->addHash($options);

        return $this;
    }

    
    public function addHash($options)
    {
        if (is_string($options)) {
            $options = [$options];
        } elseif (! is_array($options)) {
            throw new Exception\InvalidArgumentException('False parameter given');
        }

        $known = hash_algos();
        if (! isset($options['algorithm'])) {
            $algorithm = $this->options['algorithm'];
        } else {
            $algorithm = $options['algorithm'];
            unset($options['algorithm']);
        }

        if (! in_array($algorithm, $known)) {
            throw new Exception\InvalidArgumentException("Unknown algorithm '{$algorithm}'");
        }

        foreach ($options as $value) {
            if (! is_string($value)) {
                throw new Exception\InvalidArgumentException(sprintf(
                    'Hash must be a string, %s received',
                    get_debug_type($value)
                ));
            }
            $this->options['hash'][$value] = $algorithm;
        }

        return $this;
    }

    
    public function isValid($value, $file = null)
    {
        $fileInfo = $this->getFileInfo($value, $file);

        $this->setValue($fileInfo['filename']);

        
        if (empty($fileInfo['file']) || false === is_readable($fileInfo['file'])) {
            $this->error(self::NOT_FOUND);
            return false;
        }

        $algos = array_unique(array_values($this->getHash()));
        foreach ($algos as $algorithm) {
            $filehash = hash_file($algorithm, $fileInfo['file']);

            if ($filehash === false) {
                $this->error(self::NOT_DETECTED);
                return false;
            }

            if (isset($this->getHash()[$filehash]) && $this->getHash()[$filehash] === $algorithm) {
                return true;
            }
        }

        $this->error(self::DOES_NOT_MATCH);
        return false;
    }
}
