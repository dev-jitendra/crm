<?php

namespace Laminas\Validator\File;

use Laminas\Stdlib\ErrorHandler;
use Laminas\Validator\AbstractValidator;
use Laminas\Validator\Exception;
use Traversable;

use function array_shift;
use function func_get_args;
use function func_num_args;
use function getimagesize;
use function is_array;
use function is_readable;


class ImageSize extends AbstractValidator
{
    use FileInformationTrait;

    
    public const WIDTH_TOO_BIG    = 'fileImageSizeWidthTooBig';
    public const WIDTH_TOO_SMALL  = 'fileImageSizeWidthTooSmall';
    public const HEIGHT_TOO_BIG   = 'fileImageSizeHeightTooBig';
    public const HEIGHT_TOO_SMALL = 'fileImageSizeHeightTooSmall';
    public const NOT_DETECTED     = 'fileImageSizeNotDetected';
    public const NOT_READABLE     = 'fileImageSizeNotReadable';

    
    protected $messageTemplates = [
        self::WIDTH_TOO_BIG    => "Maximum allowed width for image should be '%maxwidth%' but '%width%' detected",
        self::WIDTH_TOO_SMALL  => "Minimum expected width for image should be '%minwidth%' but '%width%' detected",
        self::HEIGHT_TOO_BIG   => "Maximum allowed height for image should be '%maxheight%' but '%height%' detected",
        self::HEIGHT_TOO_SMALL => "Minimum expected height for image should be '%minheight%' but '%height%' detected",
        self::NOT_DETECTED     => 'The size of image could not be detected',
        self::NOT_READABLE     => 'File is not readable or does not exist',
    ];

    
    protected $messageVariables = [
        'minwidth'  => ['options' => 'minWidth'],
        'maxwidth'  => ['options' => 'maxWidth'],
        'minheight' => ['options' => 'minHeight'],
        'maxheight' => ['options' => 'maxHeight'],
        'width'     => 'width',
        'height'    => 'height',
    ];

    
    protected $width;

    
    protected $height;

    
    protected $options = [
        'minWidth'  => null, 
        'maxWidth'  => null, 
        'minHeight' => null, 
        'maxHeight' => null, 
    ];

    
    public function __construct($options = null)
    {
        if (1 < func_num_args()) {
            if (! is_array($options)) {
                $options = ['minWidth' => $options];
            }

            $argv = func_get_args();
            array_shift($argv);
            $options['minHeight'] = array_shift($argv);
            if (! empty($argv)) {
                $options['maxWidth'] = array_shift($argv);
                if (! empty($argv)) {
                    $options['maxHeight'] = array_shift($argv);
                }
            }
        }

        parent::__construct($options);
    }

    
    public function getMinWidth()
    {
        return $this->options['minWidth'];
    }

    
    public function setMinWidth($minWidth)
    {
        if (($this->getMaxWidth() !== null) && ($minWidth > $this->getMaxWidth())) {
            throw new Exception\InvalidArgumentException(
                'The minimum image width must be less than or equal to the '
                . " maximum image width, but {$minWidth} > {$this->getMaxWidth()}"
            );
        }

        $this->options['minWidth'] = (int) $minWidth;
        return $this;
    }

    
    public function getMaxWidth()
    {
        return $this->options['maxWidth'];
    }

    
    public function setMaxWidth($maxWidth)
    {
        if (($this->getMinWidth() !== null) && ($maxWidth < $this->getMinWidth())) {
            throw new Exception\InvalidArgumentException(
                'The maximum image width must be greater than or equal to the '
                . "minimum image width, but {$maxWidth} < {$this->getMinWidth()}"
            );
        }

        $this->options['maxWidth'] = (int) $maxWidth;
        return $this;
    }

    
    public function getMinHeight()
    {
        return $this->options['minHeight'];
    }

    
    public function setMinHeight($minHeight)
    {
        if (($this->getMaxHeight() !== null) && ($minHeight > $this->getMaxHeight())) {
            throw new Exception\InvalidArgumentException(
                'The minimum image height must be less than or equal to the '
                . " maximum image height, but {$minHeight} > {$this->getMaxHeight()}"
            );
        }

        $this->options['minHeight'] = (int) $minHeight;
        return $this;
    }

    
    public function getMaxHeight()
    {
        return $this->options['maxHeight'];
    }

    
    public function setMaxHeight($maxHeight)
    {
        if (($this->getMinHeight() !== null) && ($maxHeight < $this->getMinHeight())) {
            throw new Exception\InvalidArgumentException(
                'The maximum image height must be greater than or equal to the '
                . "minimum image height, but {$maxHeight} < {$this->getMinHeight()}"
            );
        }

        $this->options['maxHeight'] = (int) $maxHeight;
        return $this;
    }

    
    public function getImageMin()
    {
        return ['minWidth' => $this->getMinWidth(), 'minHeight' => $this->getMinHeight()];
    }

    
    public function getImageMax()
    {
        return ['maxWidth' => $this->getMaxWidth(), 'maxHeight' => $this->getMaxHeight()];
    }

    
    public function getImageWidth()
    {
        return ['minWidth' => $this->getMinWidth(), 'maxWidth' => $this->getMaxWidth()];
    }

    
    public function getImageHeight()
    {
        return ['minHeight' => $this->getMinHeight(), 'maxHeight' => $this->getMaxHeight()];
    }

    
    public function setImageMin($options)
    {
        $this->setOptions($options);
        return $this;
    }

    
    public function setImageMax($options)
    {
        $this->setOptions($options);
        return $this;
    }

    
    public function setImageWidth($options)
    {
        $this->setImageMin($options);
        $this->setImageMax($options);

        return $this;
    }

    
    public function setImageHeight($options)
    {
        $this->setImageMin($options);
        $this->setImageMax($options);

        return $this;
    }

    
    public function isValid($value, $file = null)
    {
        $fileInfo = $this->getFileInfo($value, $file);

        $this->setValue($fileInfo['filename']);

        
        if (empty($fileInfo['file']) || false === is_readable($fileInfo['file'])) {
            $this->error(self::NOT_READABLE);
            return false;
        }

        ErrorHandler::start();
        $size = getimagesize($fileInfo['file']);
        ErrorHandler::stop();

        if (empty($size) || ($size[0] === 0) || ($size[1] === 0)) {
            $this->error(self::NOT_DETECTED);
            return false;
        }

        $this->width  = $size[0];
        $this->height = $size[1];
        if ($this->width < $this->getMinWidth()) {
            $this->error(self::WIDTH_TOO_SMALL);
        }

        if (($this->getMaxWidth() !== null) && ($this->getMaxWidth() < $this->width)) {
            $this->error(self::WIDTH_TOO_BIG);
        }

        if ($this->height < $this->getMinHeight()) {
            $this->error(self::HEIGHT_TOO_SMALL);
        }

        if (($this->getMaxHeight() !== null) && ($this->getMaxHeight() < $this->height)) {
            $this->error(self::HEIGHT_TOO_BIG);
        }

        if ($this->getMessages()) {
            return false;
        }

        return true;
    }
}
