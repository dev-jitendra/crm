<?php

declare(strict_types=1);

namespace OpenSpout\Common\Helper;

use Error;
use OpenSpout\Common\Exception\EncodingConversionException;


final class EncodingHelper
{
    
    public const ENCODING_UTF8 = 'UTF-8';
    public const ENCODING_UTF16_LE = 'UTF-16LE';
    public const ENCODING_UTF16_BE = 'UTF-16BE';
    public const ENCODING_UTF32_LE = 'UTF-32LE';
    public const ENCODING_UTF32_BE = 'UTF-32BE';

    
    public const BOM_UTF8 = "\xEF\xBB\xBF";
    public const BOM_UTF16_LE = "\xFF\xFE";
    public const BOM_UTF16_BE = "\xFE\xFF";
    public const BOM_UTF32_LE = "\xFF\xFE\x00\x00";
    public const BOM_UTF32_BE = "\x00\x00\xFE\xFF";

    
    private array $supportedEncodingsWithBom;

    private readonly bool $canUseIconv;

    private readonly bool $canUseMbString;

    public function __construct(bool $canUseIconv, bool $canUseMbString)
    {
        $this->canUseIconv = $canUseIconv;
        $this->canUseMbString = $canUseMbString;

        $this->supportedEncodingsWithBom = [
            self::ENCODING_UTF8 => self::BOM_UTF8,
            self::ENCODING_UTF16_LE => self::BOM_UTF16_LE,
            self::ENCODING_UTF16_BE => self::BOM_UTF16_BE,
            self::ENCODING_UTF32_LE => self::BOM_UTF32_LE,
            self::ENCODING_UTF32_BE => self::BOM_UTF32_BE,
        ];
    }

    public static function factory(): self
    {
        return new self(
            \function_exists('iconv'),
            \function_exists('mb_convert_encoding'),
        );
    }

    
    public function getBytesOffsetToSkipBOM($filePointer, string $encoding): int
    {
        $byteOffsetToSkipBom = 0;

        if ($this->hasBOM($filePointer, $encoding)) {
            $bomUsed = $this->supportedEncodingsWithBom[$encoding];

            
            $byteOffsetToSkipBom = \strlen($bomUsed);
        }

        return $byteOffsetToSkipBom;
    }

    
    public function attemptConversionToUTF8(?string $string, string $sourceEncoding): ?string
    {
        return $this->attemptConversion($string, $sourceEncoding, self::ENCODING_UTF8);
    }

    
    public function attemptConversionFromUTF8(?string $string, string $targetEncoding): ?string
    {
        return $this->attemptConversion($string, self::ENCODING_UTF8, $targetEncoding);
    }

    
    private function hasBOM($filePointer, string $encoding): bool
    {
        $hasBOM = false;

        rewind($filePointer);

        if (\array_key_exists($encoding, $this->supportedEncodingsWithBom)) {
            $potentialBom = $this->supportedEncodingsWithBom[$encoding];
            $numBytesInBom = \strlen($potentialBom);

            $hasBOM = (fgets($filePointer, $numBytesInBom + 1) === $potentialBom);
        }

        return $hasBOM;
    }

    
    private function attemptConversion(?string $string, string $sourceEncoding, string $targetEncoding): ?string
    {
        
        if (null === $string || $sourceEncoding === $targetEncoding) {
            return $string;
        }

        $convertedString = null;

        if ($this->canUseIconv) {
            set_error_handler(static function (): bool {
                return true;
            });

            $convertedString = iconv($sourceEncoding, $targetEncoding, $string);

            restore_error_handler();
        } elseif ($this->canUseMbString) {
            $errorMessage = null;
            set_error_handler(static function ($nr, $message) use (&$errorMessage): bool {
                $errorMessage = $message; 

                return true; 
            });

            try {
                $convertedString = mb_convert_encoding($string, $targetEncoding, $sourceEncoding);
            } catch (Error $error) {
                $errorMessage = $error->getMessage();
            }

            restore_error_handler();
            if (null !== $errorMessage) {
                $convertedString = false;
            }
        } else {
            throw new EncodingConversionException("The conversion from {$sourceEncoding} to {$targetEncoding} is not supported. Please install \"iconv\" or \"mbstring\".");
        }

        if (false === $convertedString) {
            throw new EncodingConversionException("The conversion from {$sourceEncoding} to {$targetEncoding} failed.");
        }

        return $convertedString;
    }
}
