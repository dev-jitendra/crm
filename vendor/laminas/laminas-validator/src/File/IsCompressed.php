<?php

namespace Laminas\Validator\File;

use Laminas\Stdlib\ArrayUtils;
use Traversable;


class IsCompressed extends MimeType
{
    
    public const FALSE_TYPE   = 'fileIsCompressedFalseType';
    public const NOT_DETECTED = 'fileIsCompressedNotDetected';
    public const NOT_READABLE = 'fileIsCompressedNotReadable';

    
    protected $messageTemplates = [
        self::FALSE_TYPE   => "File is not compressed, '%type%' detected",
        self::NOT_DETECTED => 'The mimetype could not be detected from the file',
        self::NOT_READABLE => 'File is not readable or does not exist',
    ];

    
    public function __construct($options = [])
    {
        
        $default = [
            'application/arj',
            'application/gnutar',
            'application/lha',
            'application/lzx',
            'application/vnd.ms-cab-compressed',
            'application/x-ace-compressed',
            'application/x-arc',
            'application/x-archive',
            'application/x-arj',
            'application/x-bzip',
            'application/x-bzip2',
            'application/x-cab-compressed',
            'application/x-compress',
            'application/x-compressed',
            'application/x-cpio',
            'application/x-debian-package',
            'application/x-eet',
            'application/x-gzip',
            'application/x-java-pack200',
            'application/x-lha',
            'application/x-lharc',
            'application/x-lzh',
            'application/x-lzma',
            'application/x-lzx',
            'application/x-rar',
            'application/x-sit',
            'application/x-stuffit',
            'application/x-tar',
            'application/zip',
            'application/x-zip',
            'application/zoo',
            'multipart/x-gzip',
        ];

        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        }

        if ($options === null) {
            $options = [];
        }

        parent::__construct($options);

        if (! $this->getMimeType()) {
            $this->setMimeType($default);
        }
    }
}
