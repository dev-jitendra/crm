<?php

namespace Laminas\Validator\File;

use function array_merge;
use function class_exists;
use function explode;
use function finfo_file;
use function finfo_open;
use function in_array;
use function is_readable;

use const FILEINFO_MIME_TYPE;


class ExcludeMimeType extends MimeType
{
    use FileInformationTrait;

    public const FALSE_TYPE   = 'fileExcludeMimeTypeFalse';
    public const NOT_DETECTED = 'fileExcludeMimeTypeNotDetected';
    public const NOT_READABLE = 'fileExcludeMimeTypeNotReadable';

    
    protected $messageTemplates = [
        self::FALSE_TYPE   => "File has an incorrect mimetype of '%type%'",
        self::NOT_DETECTED => 'The mimetype could not be detected from the file',
        self::NOT_READABLE => 'File is not readable or does not exist',
    ];

    
    public function isValid($value, $file = null)
    {
        $fileInfo = $this->getFileInfo($value, $file, true);

        $this->setValue($fileInfo['filename']);

        
        if (empty($fileInfo['file']) || false === is_readable($fileInfo['file'])) {
            $this->error(self::NOT_READABLE);
            return false;
        }

        $mimefile = $this->getMagicFile();
        if (class_exists('finfo', false)) {
            if (! $this->isMagicFileDisabled() && (! empty($mimefile) && empty($this->finfo))) {
                $this->finfo = finfo_open(FILEINFO_MIME_TYPE, $mimefile);
            }

            if (empty($this->finfo)) {
                $this->finfo = finfo_open(FILEINFO_MIME_TYPE);
            }

            $this->type = null;
            if (! empty($this->finfo)) {
                $this->type = finfo_file($this->finfo, $fileInfo['file']);
            }
        }

        if (empty($this->type) && $this->getHeaderCheck()) {
            $this->type = $fileInfo['filetype'];
        }

        if (empty($this->type)) {
            $this->error(self::NOT_DETECTED);
            return false;
        }

        $mimetype = $this->getMimeType(true);
        if (in_array($this->type, $mimetype)) {
            $this->error(self::FALSE_TYPE);
            return false;
        }

        $types = explode('/', $this->type);
        $types = array_merge($types, explode('-', $this->type));
        $types = array_merge($types, explode(';', $this->type));
        foreach ($mimetype as $mime) {
            if (in_array($mime, $types)) {
                $this->error(self::FALSE_TYPE);
                return false;
            }
        }

        return true;
    }
}
