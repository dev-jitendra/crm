<?php

namespace Laminas\Mail\Header;

use Laminas\Mail\Headers;
use Laminas\Mime\Mime;

use function array_reduce;
use function explode;
use function extension_loaded;
use function iconv_mime_decode;
use function iconv_mime_encode;
use function imap_mime_header_decode;
use function imap_utf8;
use function implode;
use function str_contains;
use function str_pad;
use function str_starts_with;
use function strlen;
use function strpos;
use function substr;
use function wordwrap;

use const ICONV_MIME_DECODE_CONTINUE_ON_ERROR;



abstract class HeaderWrap
{
    
    public static function wrap($value, HeaderInterface $header)
    {
        if ($header instanceof UnstructuredInterface) {
            return static::wrapUnstructuredHeader($value, $header);
        } elseif ($header instanceof StructuredInterface) {
            return static::wrapStructuredHeader($value, $header);
        }
        return $value;
    }

    
    protected static function wrapUnstructuredHeader($value, HeaderInterface $header)
    {
        $headerNameColonSize = strlen($header->getFieldName() . ': ');
        $encoding            = $header->getEncoding();

        if ($encoding == 'ASCII') {
            
            $headerLine       = str_pad('0', $headerNameColonSize, '0') . $value;
            $foldedHeaderLine = wordwrap($headerLine, 78, Headers::FOLDING);

            
            return substr($foldedHeaderLine, $headerNameColonSize);
        }

        return static::mimeEncodeValue($value, $encoding, 78, $headerNameColonSize);
    }

    
    protected static function wrapStructuredHeader($value, StructuredInterface $header)
    {
        $delimiter = $header->getDelimiter();

        $length = strlen($value);
        $lines  = [];
        $temp   = '';
        for ($i = 0; $i < $length; $i++) {
            $temp .= $value[$i];
            if ($value[$i] == $delimiter) {
                $lines[] = $temp;
                $temp    = '';
            }
        }
        return implode(Headers::FOLDING, $lines);
    }

    
    public static function mimeEncodeValue($value, $encoding, $lineLength = 998, $firstLineGapSize = 0)
    {
        return Mime::encodeQuotedPrintableHeader($value, $encoding, $lineLength, Headers::EOL, $firstLineGapSize);
    }

    
    public static function mimeDecodeValue($value)
    {
        
        

        
        $parts = explode(Headers::FOLDING, $value);
        $value = implode(' ', $parts);

        $decodedValue = iconv_mime_decode($value, ICONV_MIME_DECODE_CONTINUE_ON_ERROR, 'UTF-8');

        
        if (self::isNotDecoded($value, $decodedValue) && extension_loaded('imap')) {
            return array_reduce(
                imap_mime_header_decode(imap_utf8($value)),
                static fn($accumulator, $headerPart) => $accumulator . $headerPart->text,
                ''
            );
        }

        return $decodedValue;
    }

    private static function isNotDecoded(string $originalValue, string $value): bool
    {
        return str_starts_with($value, '=?')
            && strlen($value) - 2 === strpos($value, '?=')
            && str_contains($originalValue, $value);
    }

    
    public static function canBeEncoded($value)
    {
        
        
        
        
        $charset    = 'UTF-8';
        $lineLength = strlen($value) * 4 + strlen($charset) + 16;

        $preferences = [
            'scheme'         => 'Q',
            'input-charset'  => $charset,
            'output-charset' => $charset,
            'line-length'    => $lineLength,
        ];

        $encoded = iconv_mime_encode('x-test', $value, $preferences);

        return false !== $encoded;
    }
}
