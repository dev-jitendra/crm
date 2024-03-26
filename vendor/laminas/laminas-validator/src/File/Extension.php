<?php

namespace Laminas\Validator\File;

use Laminas\Stdlib\ArrayUtils;
use Laminas\Validator\AbstractValidator;
use Traversable;

use function array_key_exists;
use function array_unique;
use function explode;
use function func_get_arg;
use function func_num_args;
use function implode;
use function in_array;
use function is_array;
use function is_readable;
use function is_string;
use function strrpos;
use function strtolower;
use function substr;
use function trim;


class Extension extends AbstractValidator
{
    use FileInformationTrait;

    
    public const FALSE_EXTENSION = 'fileExtensionFalse';
    public const NOT_FOUND       = 'fileExtensionNotFound';

    
    protected $messageTemplates = [
        self::FALSE_EXTENSION => 'File has an incorrect extension',
        self::NOT_FOUND       => 'File is not readable or does not exist',
    ];

    
    protected $options = [
        'case'                 => false, 
        'extension'            => '', 
        'allowNonExistentFile' => false, 
    ];

    
    protected $messageVariables = [
        'extension' => ['options' => 'extension'],
    ];

    
    public function __construct($options = null)
    {
        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        }

        $case = null;
        if (1 < func_num_args()) {
            $case = func_get_arg(1);
        }

        if (is_array($options)) {
            if (isset($options['case'])) {
                $case = $options['case'];
                unset($options['case']);
            }

            if (! array_key_exists('extension', $options)) {
                $options = ['extension' => $options];
            }
        } else {
            $options = ['extension' => $options];
        }

        if ($case !== null) {
            $options['case'] = $case;
        }

        parent::__construct($options);
    }

    
    public function getCase()
    {
        return $this->options['case'];
    }

    
    public function setCase($case)
    {
        $this->options['case'] = (bool) $case;
        return $this;
    }

    
    public function getExtension()
    {
        if (
            ! array_key_exists('extension', $this->options)
            || ! is_string($this->options['extension'])
        ) {
            return [];
        }

        return explode(',', $this->options['extension']);
    }

    
    public function setExtension($extension)
    {
        $this->options['extension'] = null;
        $this->addExtension($extension);
        return $this;
    }

    
    public function addExtension($extension)
    {
        $extensions = $this->getExtension();
        if (is_string($extension)) {
            $extension = explode(',', $extension);
        }

        foreach ($extension as $content) {
            if (empty($content) || ! is_string($content)) {
                continue;
            }

            $extensions[] = trim($content);
        }

        $extensions = array_unique($extensions);

        
        foreach ($extensions as $key => $ext) {
            if (empty($ext)) {
                unset($extensions[$key]);
            }
        }

        $this->options['extension'] = implode(',', $extensions);
        return $this;
    }

    
    public function getAllowNonExistentFile()
    {
        return $this->options['allowNonExistentFile'];
    }

    
    public function setAllowNonExistentFile($flag)
    {
        $this->options['allowNonExistentFile'] = (bool) $flag;
        return $this;
    }

    
    public function isValid($value, $file = null)
    {
        $fileInfo = $this->getFileInfo($value, $file);

        
        if (
            ! $this->getAllowNonExistentFile()
            && (empty($fileInfo['file']) || false === is_readable($fileInfo['file']))
        ) {
            $this->error(self::NOT_FOUND);
            return false;
        }

        $this->setValue($fileInfo['filename']);

        $extension  = substr($fileInfo['filename'], strrpos($fileInfo['filename'], '.') + 1);
        $extensions = $this->getExtension();

        if ($this->getCase() && (in_array($extension, $extensions))) {
            return true;
        } elseif (! $this->getCase()) {
            foreach ($extensions as $ext) {
                if (strtolower($ext) === strtolower($extension)) {
                    return true;
                }
            }
        }

        $this->error(self::FALSE_EXTENSION);
        return false;
    }
}
