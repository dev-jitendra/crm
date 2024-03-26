<?php

namespace Laminas\Validator\File;

use function file_exists;

use const DIRECTORY_SEPARATOR;


class NotExists extends Exists
{
    use FileInformationTrait;

    
    public const DOES_EXIST = 'fileNotExistsDoesExist';

    
    protected $messageTemplates = [
        self::DOES_EXIST => 'File exists',
    ];

    
    public function isValid($value, $file = null)
    {
        $fileInfo = $this->getFileInfo($value, $file, false, true);

        $this->setValue($fileInfo['filename']);

        $check       = false;
        $directories = $this->getDirectory(true);
        if (! isset($directories)) {
            $check = true;
            if (file_exists($fileInfo['file'])) {
                $this->error(self::DOES_EXIST);
                return false;
            }
        } else {
            foreach ($directories as $directory) {
                if (! isset($directory) || '' === $directory) {
                    continue;
                }

                $check = true;
                if (file_exists($directory . DIRECTORY_SEPARATOR . $fileInfo['basename'])) {
                    $this->error(self::DOES_EXIST);
                    return false;
                }
            }
        }

        if (! $check) {
            $this->error(self::DOES_EXIST);
            return false;
        }

        return true;
    }
}
