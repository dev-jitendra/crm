<?php

namespace Laminas\Validator\File;

use Laminas\Validator\AbstractValidator;
use Laminas\Validator\Exception;
use Psr\Http\Message\UploadedFileInterface;

use function basename;
use function is_array;
use function is_file;
use function is_string;
use function is_uploaded_file;

use const UPLOAD_ERR_CANT_WRITE;
use const UPLOAD_ERR_EXTENSION;
use const UPLOAD_ERR_FORM_SIZE;
use const UPLOAD_ERR_INI_SIZE;
use const UPLOAD_ERR_NO_FILE;
use const UPLOAD_ERR_NO_TMP_DIR;
use const UPLOAD_ERR_OK;
use const UPLOAD_ERR_PARTIAL;


class UploadFile extends AbstractValidator
{
    
    public const INI_SIZE       = 'fileUploadFileErrorIniSize';
    public const FORM_SIZE      = 'fileUploadFileErrorFormSize';
    public const PARTIAL        = 'fileUploadFileErrorPartial';
    public const NO_FILE        = 'fileUploadFileErrorNoFile';
    public const NO_TMP_DIR     = 'fileUploadFileErrorNoTmpDir';
    public const CANT_WRITE     = 'fileUploadFileErrorCantWrite';
    public const EXTENSION      = 'fileUploadFileErrorExtension';
    public const ATTACK         = 'fileUploadFileErrorAttack';
    public const FILE_NOT_FOUND = 'fileUploadFileErrorFileNotFound';
    public const UNKNOWN        = 'fileUploadFileErrorUnknown';

    
    protected $messageTemplates = [
        self::INI_SIZE       => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
        self::FORM_SIZE      => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was '
            . 'specified in the HTML form',
        self::PARTIAL        => 'The uploaded file was only partially uploaded',
        self::NO_FILE        => 'No file was uploaded',
        self::NO_TMP_DIR     => 'Missing a temporary folder',
        self::CANT_WRITE     => 'Failed to write file to disk',
        self::EXTENSION      => 'A PHP extension stopped the file upload',
        self::ATTACK         => 'File was illegally uploaded. This could be a possible attack',
        self::FILE_NOT_FOUND => 'File was not found',
        self::UNKNOWN        => 'Unknown error while uploading file',
    ];

    
    public function isValid($value)
    {
        if (is_array($value)) {
            if (! isset($value['tmp_name']) || ! isset($value['name']) || ! isset($value['error'])) {
                throw new Exception\InvalidArgumentException(
                    'Value array must be in $_FILES format'
                );
            }

            return $this->validateUploadedFile(
                $value['error'],
                $value['name'],
                $value['tmp_name']
            );
        }

        if ($value instanceof UploadedFileInterface) {
            return $this->validatePsr7UploadedFile($value);
        }

        if (is_string($value)) {
            return $this->validateUploadedFile(0, basename($value), $value);
        }

        $this->error(self::UNKNOWN);
        return false;
    }

    
    private function validateFileFromErrorCode($error)
    {
        switch ($error) {
            case UPLOAD_ERR_OK:
                return true;

            case UPLOAD_ERR_INI_SIZE:
                $this->error(self::INI_SIZE);
                return false;

            case UPLOAD_ERR_FORM_SIZE:
                $this->error(self::FORM_SIZE);
                return false;

            case UPLOAD_ERR_PARTIAL:
                $this->error(self::PARTIAL);
                return false;

            case UPLOAD_ERR_NO_FILE:
                $this->error(self::NO_FILE);
                return false;

            case UPLOAD_ERR_NO_TMP_DIR:
                $this->error(self::NO_TMP_DIR);
                return false;

            case UPLOAD_ERR_CANT_WRITE:
                $this->error(self::CANT_WRITE);
                return false;

            case UPLOAD_ERR_EXTENSION:
                $this->error(self::EXTENSION);
                return false;

            default:
                $this->error(self::UNKNOWN);
                return false;
        }
    }

    
    private function validateUploadedFile($error, $filename, $uploadedFile)
    {
        $this->setValue($filename);

        
        if ($error !== UPLOAD_ERR_OK) {
            return $this->validateFileFromErrorCode($error);
        }

        
        if (empty($uploadedFile) || false === is_file($uploadedFile)) {
            $this->error(self::FILE_NOT_FOUND);
            return false;
        }

        
        if (! is_uploaded_file($uploadedFile)) {
            $this->error(self::ATTACK);
            return false;
        }

        return true;
    }

    
    private function validatePsr7UploadedFile(UploadedFileInterface $uploadedFile)
    {
        $this->setValue($uploadedFile);
        return $this->validateFileFromErrorCode($uploadedFile->getError());
    }
}
