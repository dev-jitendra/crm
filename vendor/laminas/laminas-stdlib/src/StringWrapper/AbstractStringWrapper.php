<?php

declare(strict_types=1);

namespace Laminas\Stdlib\StringWrapper;

use Laminas\Stdlib\Exception;
use Laminas\Stdlib\StringUtils;

use function floor;
use function in_array;
use function sprintf;
use function str_pad;
use function str_repeat;
use function strtoupper;
use function wordwrap;

use const STR_PAD_BOTH;
use const STR_PAD_LEFT;
use const STR_PAD_RIGHT;

abstract class AbstractStringWrapper implements StringWrapperInterface
{
    
    protected $encoding = 'UTF-8';

    
    protected $convertEncoding;

    
    public static function isSupported($encoding, $convertEncoding = null)
    {
        $supportedEncodings = static::getSupportedEncodings();

        if (! in_array(strtoupper($encoding), $supportedEncodings)) {
            return false;
        }

        if ($convertEncoding !== null && ! in_array(strtoupper($convertEncoding), $supportedEncodings)) {
            return false;
        }

        return true;
    }

    
    public function setEncoding($encoding, $convertEncoding = null)
    {
        $supportedEncodings = static::getSupportedEncodings();

        $encodingUpper = strtoupper($encoding);
        if (! in_array($encodingUpper, $supportedEncodings)) {
            throw new Exception\InvalidArgumentException(
                'Wrapper doesn\'t support character encoding "' . $encoding . '"'
            );
        }

        if ($convertEncoding !== null) {
            $convertEncodingUpper = strtoupper($convertEncoding);
            if (! in_array($convertEncodingUpper, $supportedEncodings)) {
                throw new Exception\InvalidArgumentException(
                    'Wrapper doesn\'t support character encoding "' . $convertEncoding . '"'
                );
            }

            $this->convertEncoding = $convertEncodingUpper;
        } else {
            $this->convertEncoding = null;
        }
        $this->encoding = $encodingUpper;

        return $this;
    }

    
    public function getEncoding()
    {
        return $this->encoding;
    }

    
    public function getConvertEncoding()
    {
        return $this->convertEncoding;
    }

    
    public function convert($str, $reverse = false)
    {
        $encoding        = $this->getEncoding();
        $convertEncoding = $this->getConvertEncoding();
        if ($convertEncoding === null) {
            throw new Exception\LogicException(
                'No convert encoding defined'
            );
        }

        if ($encoding === $convertEncoding) {
            return $str;
        }

        $from = $reverse ? $convertEncoding : $encoding;
        $to   = $reverse ? $encoding : $convertEncoding;
        throw new Exception\RuntimeException(sprintf(
            'Converting from "%s" to "%s" isn\'t supported by this string wrapper',
            $from ?? '',
            $to ?? ''
        ));
    }

    
    public function wordWrap($string, $width = 75, $break = "\n", $cut = false)
    {
        $string = (string) $string;
        if ($string === '') {
            return '';
        }

        $break = (string) $break;
        if ($break === '') {
            throw new Exception\InvalidArgumentException('Break string cannot be empty');
        }

        $width = (int) $width;
        if ($width === 0 && $cut) {
            throw new Exception\InvalidArgumentException('Cannot force cut when width is zero');
        }

        if (null === $this->getEncoding() || StringUtils::isSingleByteEncoding($this->getEncoding())) {
            return wordwrap($string, $width, $break, $cut);
        }

        $stringWidth = $this->strlen($string);
        $breakWidth  = $this->strlen($break);

        $result    = '';
        $lastStart = $lastSpace = 0;

        for ($current = 0; $current < $stringWidth; $current++) {
            $char = $this->substr($string, $current, 1);

            $possibleBreak = $char;
            if ($breakWidth !== 1) {
                $possibleBreak = $this->substr($string, $current, $breakWidth);
            }

            if ($possibleBreak === $break) {
                $result   .= $this->substr($string, $lastStart, $current - $lastStart + $breakWidth);
                $current  += $breakWidth - 1;
                $lastStart = $lastSpace = $current + 1;
                continue;
            }

            if ($char === ' ') {
                if ($current - $lastStart >= $width) {
                    $result   .= $this->substr($string, $lastStart, $current - $lastStart) . $break;
                    $lastStart = $current + 1;
                }

                $lastSpace = $current;
                continue;
            }

            if ($current - $lastStart >= $width && $cut && $lastStart >= $lastSpace) {
                $result   .= $this->substr($string, $lastStart, $current - $lastStart) . $break;
                $lastStart = $lastSpace = $current;
                continue;
            }

            if ($current - $lastStart >= $width && $lastStart < $lastSpace) {
                $result   .= $this->substr($string, $lastStart, $lastSpace - $lastStart) . $break;
                $lastStart = $lastSpace += 1;
                continue;
            }
        }

        if ($lastStart !== $current) {
            $result .= $this->substr($string, $lastStart, $current - $lastStart);
        }

        return $result;
    }

    
    public function strPad($input, $padLength, $padString = ' ', $padType = STR_PAD_RIGHT)
    {
        if (null === $this->getEncoding() || StringUtils::isSingleByteEncoding($this->getEncoding())) {
            return str_pad($input, $padLength, $padString, $padType);
        }

        $lengthOfPadding = $padLength - $this->strlen($input);
        if ($lengthOfPadding <= 0) {
            return $input;
        }

        $padStringLength = $this->strlen($padString);
        if ($padStringLength === 0) {
            return $input;
        }

        $repeatCount = (int) floor($lengthOfPadding / $padStringLength);

        if ($padType === STR_PAD_BOTH) {
            $repeatCountLeft = $repeatCountRight = ($repeatCount - $repeatCount % 2) / 2;

            $lastStringLength       = $lengthOfPadding - 2 * $repeatCountLeft * $padStringLength;
            $lastStringLeftLength   = $lastStringRightLength = (int) floor($lastStringLength / 2);
            $lastStringRightLength += $lastStringLength % 2;

            $lastStringLeft  = $this->substr($padString, 0, $lastStringLeftLength);
            $lastStringRight = $this->substr($padString, 0, $lastStringRightLength);

            return str_repeat($padString, $repeatCountLeft) . $lastStringLeft
                . $input
                . str_repeat($padString, $repeatCountRight) . $lastStringRight;
        }

        $lastString = $this->substr($padString, 0, $lengthOfPadding % $padStringLength);

        if ($padType === STR_PAD_LEFT) {
            return str_repeat($padString, $repeatCount) . $lastString . $input;
        }

        return $input . str_repeat($padString, $repeatCount) . $lastString;
    }
}
