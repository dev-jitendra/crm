<?php

namespace Laminas\Validator\File;

use Laminas\Validator\AbstractValidator;
use Laminas\Validator\Exception;
use Traversable;

use function array_key_exists;
use function array_unique;
use function explode;
use function file_exists;
use function implode;
use function is_array;
use function is_string;
use function trim;

use const DIRECTORY_SEPARATOR;


class Exists extends AbstractValidator
{
    use FileInformationTrait;

    
    public const DOES_NOT_EXIST = 'fileExistsDoesNotExist';

    
    protected $messageTemplates = [
        self::DOES_NOT_EXIST => 'File does not exist',
    ];

    
    protected $options = [
        'directory' => null, 
    ];

    
    protected $messageVariables = [
        'directory' => ['options' => 'directory'],
    ];

    
    public function __construct($options = null)
    {
        if (is_string($options)) {
            $options = explode(',', $options);
        }

        if (is_array($options) && ! array_key_exists('directory', $options)) {
            $options = ['directory' => $options];
        }

        parent::__construct($options);
    }

    
    public function getDirectory($asArray = false)
    {
        $asArray   = (bool) $asArray;
        $directory = $this->options['directory'];
        if ($asArray && isset($directory)) {
            $directory = explode(',', (string) $directory);
        }

        return $directory;
    }

    
    public function setDirectory($directory)
    {
        $this->options['directory'] = null;
        $this->addDirectory($directory);
        return $this;
    }

    
    public function addDirectory($directory)
    {
        $directories = $this->getDirectory(true);
        if (! isset($directories)) {
            $directories = [];
        }

        if (is_string($directory)) {
            $directory = explode(',', $directory);
        } elseif (! is_array($directory)) {
            throw new Exception\InvalidArgumentException('Invalid options to validator provided');
        }

        foreach ($directory as $content) {
            if (empty($content) || ! is_string($content)) {
                continue;
            }

            $directories[] = trim($content);
        }
        $directories = array_unique($directories);

        
        foreach ($directories as $key => $dir) {
            if (empty($dir)) {
                unset($directories[$key]);
            }
        }

        $this->options['directory'] = ! empty($directory)
            ? implode(',', $directories) : null;

        return $this;
    }

    
    public function isValid($value, $file = null)
    {
        $fileInfo = $this->getFileInfo($value, $file, false, true);

        $this->setValue($fileInfo['filename']);

        $check       = false;
        $directories = $this->getDirectory(true);
        if (! isset($directories)) {
            $check = true;
            if (! file_exists($fileInfo['file'])) {
                $this->error(self::DOES_NOT_EXIST);
                return false;
            }
        } else {
            foreach ($directories as $directory) {
                if (! isset($directory) || '' === $directory) {
                    continue;
                }

                $check = true;
                if (! file_exists($directory . DIRECTORY_SEPARATOR . $fileInfo['basename'])) {
                    $this->error(self::DOES_NOT_EXIST);
                    return false;
                }
            }
        }

        if (! $check) {
            $this->error(self::DOES_NOT_EXIST);
            return false;
        }

        return true;
    }
}
