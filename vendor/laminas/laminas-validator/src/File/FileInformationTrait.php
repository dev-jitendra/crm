<?php

namespace Laminas\Validator\File;

use Laminas\Validator\Exception;
use Psr\Http\Message\UploadedFileInterface;

use function basename;
use function is_array;
use function is_string;

trait FileInformationTrait
{
    
    protected function getFileInfo(
        $value,
        ?array $file = null,
        $hasType = false,
        $hasBasename = false
    ) {
        if (is_string($value) && is_array($file)) {
            return $this->getLegacyFileInfo($file, $hasType, $hasBasename);
        }

        if (is_array($value)) {
            return $this->getSapiFileInfo($value, $hasType, $hasBasename);
        }

        if ($value instanceof UploadedFileInterface) {
            return $this->getPsr7FileInfo($value, $hasType, $hasBasename);
        }

        return $this->getFileBasedFileInfo($value, $hasType, $hasBasename);
    }

    
    private function getLegacyFileInfo(
        array $file,
        $hasType = false,
        $hasBasename = false
    ) {
        $fileInfo = [];

        $fileInfo['filename'] = $file['name'];
        $fileInfo['file']     = $file['tmp_name'];

        if ($hasBasename) {
            $fileInfo['basename'] = basename($fileInfo['file']);
        }

        if ($hasType) {
            $fileInfo['filetype'] = $file['type'];
        }

        return $fileInfo;
    }

    
    private function getSapiFileInfo(
        array $file,
        $hasType = false,
        $hasBasename = false
    ) {
        if (! isset($file['tmp_name']) || ! isset($file['name'])) {
            throw new Exception\InvalidArgumentException(
                'Value array must be in $_FILES format'
            );
        }

        $fileInfo = [];

        $fileInfo['file']     = $file['tmp_name'];
        $fileInfo['filename'] = $file['name'];

        if ($hasBasename) {
            $fileInfo['basename'] = basename($fileInfo['file']);
        }

        if ($hasType) {
            $fileInfo['filetype'] = $file['type'];
        }

        return $fileInfo;
    }

    
    private function getPsr7FileInfo(
        UploadedFileInterface $file,
        $hasType = false,
        $hasBasename = false
    ) {
        $fileInfo = [];

        $fileInfo['file']     = $file->getStream()->getMetadata('uri');
        $fileInfo['filename'] = $file->getClientFilename();

        if ($hasBasename) {
            $fileInfo['basename'] = basename($fileInfo['file']);
        }

        if ($hasType) {
            $fileInfo['filetype'] = $file->getClientMediaType();
        }

        return $fileInfo;
    }

    
    private function getFileBasedFileInfo(
        $file,
        $hasType = false,
        $hasBasename = false
    ) {
        $fileInfo = [];

        $fileInfo['file']     = $file;
        $fileInfo['filename'] = basename($fileInfo['file']);

        if ($hasBasename) {
            $fileInfo['basename'] = basename($fileInfo['file']);
        }

        if ($hasType) {
            $fileInfo['filetype'] = null;
        }

        return $fileInfo;
    }
}
