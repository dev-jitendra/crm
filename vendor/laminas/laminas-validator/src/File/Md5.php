<?php

namespace Laminas\Validator\File;

use function array_keys;
use function array_unique;
use function hash_file;
use function is_readable;


class Md5 extends Hash
{
    use FileInformationTrait;

    
    public const DOES_NOT_MATCH = 'fileMd5DoesNotMatch';
    public const NOT_DETECTED   = 'fileMd5NotDetected';
    public const NOT_FOUND      = 'fileMd5NotFound';

    
    protected $messageTemplates = [
        self::DOES_NOT_MATCH => 'File does not match the given md5 hashes',
        self::NOT_DETECTED   => 'An md5 hash could not be evaluated for the given file',
        self::NOT_FOUND      => 'File is not readable or does not exist',
    ];

    
    protected $options = [
        'algorithm' => 'md5',
        'hash'      => null,
    ];

    
    public function getMd5()
    {
        return $this->getHash();
    }

    
    public function setMd5($options)
    {
        $this->setHash($options);
        return $this;
    }

    
    public function addMd5($options)
    {
        $this->addHash($options);
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

        $hashes   = array_unique(array_keys($this->getHash()));
        $filehash = hash_file('md5', $fileInfo['file']);
        if ($filehash === false) {
            $this->error(self::NOT_DETECTED);
            return false;
        }

        foreach ($hashes as $hash) {
            if ($filehash === $hash) {
                return true;
            }
        }

        $this->error(self::DOES_NOT_MATCH);
        return false;
    }
}
