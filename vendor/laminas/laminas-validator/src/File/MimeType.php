<?php

namespace Laminas\Validator\File;

use finfo;
use Laminas\Stdlib\ArrayUtils;
use Laminas\Stdlib\ErrorHandler;
use Laminas\Validator\AbstractValidator;
use Laminas\Validator\Exception;
use Traversable;

use function array_key_exists;
use function array_keys;
use function array_merge;
use function array_unique;
use function class_exists;
use function explode;
use function finfo_file;
use function finfo_open;
use function getenv;
use function implode;
use function in_array;
use function is_array;
use function is_file;
use function is_int;
use function is_readable;
use function is_string;
use function sprintf;
use function trim;

use const E_NOTICE;
use const E_WARNING;
use const FILEINFO_MIME_TYPE;


class MimeType extends AbstractValidator
{
    use FileInformationTrait;

    
    public const FALSE_TYPE   = 'fileMimeTypeFalse';
    public const NOT_DETECTED = 'fileMimeTypeNotDetected';
    public const NOT_READABLE = 'fileMimeTypeNotReadable';
    

    
    protected $messageTemplates = [
        self::FALSE_TYPE   => "File has an incorrect mimetype of '%type%'",
        self::NOT_DETECTED => 'The mimetype could not be detected from the file',
        self::NOT_READABLE => 'File is not readable or does not exist',
    ];

    
    protected $messageVariables = [
        'type' => 'type',
    ];

    
    protected $type;

    
    protected $finfo;

    
    protected $magicFiles = [
        '/usr/share/misc/magic',
        '/usr/share/misc/magic.mime',
        '/usr/share/misc/magic.mgc',
        '/usr/share/mime/magic',
        '/usr/share/mime/magic.mime',
        '/usr/share/mime/magic.mgc',
        '/usr/share/file/magic',
        '/usr/share/file/magic.mime',
        '/usr/share/file/magic.mgc',
    ];

    
    protected $options = [
        'enableHeaderCheck' => false, 
        'disableMagicFile'  => false, 
        'magicFile'         => null, 
        'mimeType'          => null, 
    ];

    
    public function __construct($options = null)
    {
        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        } elseif (is_string($options)) {
            $this->setMimeType($options);
            $options = [];
        } elseif (is_array($options)) {
            if (isset($options['magicFile'])) {
                $this->setMagicFile($options['magicFile']);
                unset($options['magicFile']);
            }

            if (isset($options['enableHeaderCheck'])) {
                $this->enableHeaderCheck($options['enableHeaderCheck']);
                unset($options['enableHeaderCheck']);
            }

            if (array_key_exists('mimeType', $options)) {
                $this->setMimeType($options['mimeType']);
                unset($options['mimeType']);
            }

            
            
            foreach (array_keys($options) as $key) {
                if (! is_int($key)) {
                    continue;
                }
                $this->addMimeType($options[$key]);
                unset($options[$key]);
            }
        }

        parent::__construct($options);
    }

    
    public function getMagicFile()
    {
        if (null === $this->options['magicFile']) {
            $magic = getenv('magic');
            if (! empty($magic)) {
                $this->setMagicFile($magic);
                if ($this->options['magicFile'] === null) {
                    $this->options['magicFile'] = false;
                }
                return $this->options['magicFile'];
            }

            foreach ($this->magicFiles as $file) {
                try {
                    $this->setMagicFile($file);
                } catch (Exception\ExceptionInterface) {
                    
                    continue;
                }

                if ($this->options['magicFile'] !== null) {
                    return $this->options['magicFile'];
                }
            }

            if ($this->options['magicFile'] === null) {
                $this->options['magicFile'] = false;
            }
        }

        return $this->options['magicFile'];
    }

    
    public function setMagicFile($file)
    {
        if ($file === false) {
            $this->options['magicFile'] = false;
        } elseif (empty($file)) {
            $this->options['magicFile'] = null;
        } elseif (! class_exists('finfo', false)) {
            $this->options['magicFile'] = null;
            throw new Exception\RuntimeException('Magicfile can not be set; there is no finfo extension installed');
        } elseif (! is_file($file) || ! is_readable($file)) {
            throw new Exception\InvalidArgumentException(sprintf(
                'The given magicfile ("%s") could not be read',
                $file
            ));
        } else {
            ErrorHandler::start(E_NOTICE | E_WARNING);
            $this->finfo = finfo_open(FILEINFO_MIME_TYPE, $file);
            $error       = ErrorHandler::stop();
            if (empty($this->finfo)) {
                $this->finfo = null;
                throw new Exception\InvalidMagicMimeFileException(sprintf(
                    'The given magicfile ("%s") could not be used by ext/finfo',
                    $file
                ), 0, $error);
            }
            $this->options['magicFile'] = $file;
        }

        return $this;
    }

    
    public function disableMagicFile($disable)
    {
        $this->options['disableMagicFile'] = (bool) $disable;
        return $this;
    }

    
    public function isMagicFileDisabled()
    {
        return $this->options['disableMagicFile'];
    }

    
    public function getHeaderCheck()
    {
        return $this->options['enableHeaderCheck'];
    }

    
    public function enableHeaderCheck($headerCheck = true)
    {
        $this->options['enableHeaderCheck'] = (bool) $headerCheck;
        return $this;
    }

    
    public function getMimeType($asArray = false)
    {
        $asArray  = (bool) $asArray;
        $mimetype = (string) $this->options['mimeType'];
        if ($asArray) {
            $mimetype = explode(',', $mimetype);
        }

        return $mimetype;
    }

    
    public function setMimeType($mimetype)
    {
        $this->options['mimeType'] = null;
        $this->addMimeType($mimetype);
        return $this;
    }

    
    public function addMimeType($mimetype)
    {
        $mimetypes = $this->getMimeType(true);

        if (is_string($mimetype)) {
            $mimetype = explode(',', $mimetype);
        } elseif (! is_array($mimetype)) {
            throw new Exception\InvalidArgumentException('Invalid options to validator provided');
        }

        if (isset($mimetype['magicFile'])) {
            unset($mimetype['magicFile']);
        }

        foreach ($mimetype as $content) {
            if (empty($content) || ! is_string($content)) {
                continue;
            }
            $mimetypes[] = trim($content);
        }
        $mimetypes = array_unique($mimetypes);

        
        foreach ($mimetypes as $key => $mt) {
            if (empty($mt)) {
                unset($mimetypes[$key]);
            }
        }

        $this->options['mimeType'] = implode(',', $mimetypes);

        return $this;
    }

    
    public function isValid($value, $file = null)
    {
        $fileInfo = $this->getFileInfo($value, $file, true);

        $this->setValue($fileInfo['filename']);

        
        if (empty($fileInfo['file']) || false === is_readable($fileInfo['file'])) {
            $this->error(static::NOT_READABLE);
            return false;
        }

        $mimefile = $this->getMagicFile();
        if (class_exists('finfo', false)) {
            if (! $this->isMagicFileDisabled() && (! empty($mimefile) && empty($this->finfo))) {
                ErrorHandler::start(E_NOTICE | E_WARNING);
                $this->finfo = finfo_open(FILEINFO_MIME_TYPE, $mimefile);
                ErrorHandler::stop();
            }

            if (empty($this->finfo)) {
                ErrorHandler::start(E_NOTICE | E_WARNING);
                $this->finfo = finfo_open(FILEINFO_MIME_TYPE);
                ErrorHandler::stop();
            }

            $this->type = null;
            if (! empty($this->finfo)) {
                $this->type = finfo_file($this->finfo, $fileInfo['file']);
                unset($this->finfo);
            }
        }

        if (empty($this->type) && $this->getHeaderCheck()) {
            $this->type = $fileInfo['filetype'];
        }

        if (empty($this->type)) {
            $this->error(static::NOT_DETECTED);
            return false;
        }

        $mimetype = $this->getMimeType(true);
        if (in_array($this->type, $mimetype)) {
            return true;
        }

        $types = explode('/', $this->type);
        $types = array_merge($types, explode('-', $this->type));
        $types = array_merge($types, explode(';', $this->type));
        foreach ($mimetype as $mime) {
            if (in_array($mime, $types)) {
                return true;
            }
        }

        $this->error(static::FALSE_TYPE);
        return false;
    }
}
