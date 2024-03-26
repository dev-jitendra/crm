<?php 

namespace Laminas\Mime;

use Laminas\Mail\Headers;
use Laminas\Stdlib\ErrorHandler;

use function count;
use function explode;
use function iconv_mime_decode;
use function preg_match;
use function preg_match_all;
use function preg_split;
use function str_replace;
use function strcasecmp;
use function strlen;
use function strpos;
use function strtok;
use function strtolower;
use function substr;

use const E_NOTICE;
use const E_WARNING;
use const ICONV_MIME_DECODE_CONTINUE_ON_ERROR;

class Decode
{
    
    public static function splitMime($body, $boundary)
    {
        
        $body = str_replace("\r", '', $body);

        $start = 0;
        $res   = [];
        
        
        
        $p = strpos($body, '--' . $boundary . "\n", $start);
        if ($p === false) {
            
            return [];
        }

        
        $start = $p + 3 + strlen($boundary);

        while (($p = strpos($body, '--' . $boundary . "\n", $start)) !== false) {
            $res[] = substr($body, $start, $p - $start);
            $start = $p + 3 + strlen($boundary);
        }

        
        $p = strpos($body, '--' . $boundary . '--', $start);
        if ($p === false) {
            throw new Exception\RuntimeException('Not a valid Mime Message: End Missing');
        }

        
        $res[] = substr($body, $start, $p - $start);
        return $res;
    }

    
    public static function splitMessageStruct($message, $boundary, $EOL = Mime::LINEEND)
    {
        $parts = static::splitMime($message, $boundary);
        if (! $parts) {
            return;
        }
        $result  = [];
        $headers = null; 
        $body    = null; 
        foreach ($parts as $part) {
            static::splitMessage($part, $headers, $body, $EOL);
            $result[] = [
                'header' => $headers,
                'body'   => $body,
            ];
        }
        return $result;
    }

    
    public static function splitMessage($message, &$headers, &$body, $EOL = Mime::LINEEND, $strict = false)
    {
        if ($message instanceof Headers) {
            $message = $message->toString();
        }
        
        $firstlinePos = strpos($message, "\n");
        $firstline    = $firstlinePos === false ? $message : substr($message, 0, $firstlinePos);
        if (! preg_match('%^[^\s]+[^:]*:%', $firstline)) {
            $headers = new Headers();
            
            $body = str_replace(["\r", "\n"], ['', $EOL], $message);
            return;
        }

        
        if (! $strict) {
            $parts = explode(':', $firstline, 2);
            if (count($parts) !== 2) {
                $message = substr($message, strpos($message, $EOL) + 1);
            }
        }

        
        
        $headersEOL = $EOL;

        
        
        
        if (strpos($message, $EOL . $EOL)) {
            [$headers, $body] = explode($EOL . $EOL, $message, 2);
        
        } elseif ($EOL !== "\r\n" && strpos($message, "\r\n\r\n")) {
            [$headers, $body] = explode("\r\n\r\n", $message, 2);
            $headersEOL       = "\r\n"; 
        
        } elseif ($EOL !== "\n" && strpos($message, "\n\n")) {
            [$headers, $body] = explode("\n\n", $message, 2);
            $headersEOL       = "\n";
        
        } else {
            ErrorHandler::start(E_NOTICE | E_WARNING);
            [$headers, $body] = preg_split("%([\r\n]+)\\1%U", $message, 2);
            ErrorHandler::stop();
        }

        $headers = Headers::fromString($headers, $headersEOL);
    }

    
    public static function splitContentType($type, $wantedPart = null)
    {
        return static::splitHeaderField($type, $wantedPart, 'type');
    }

    
    public static function splitHeaderField($field, $wantedPart = null, $firstName = '0')
    {
        $wantedPart = strtolower($wantedPart ?? '');
        $firstName  = strtolower($firstName);

        
        if ($firstName === $wantedPart) {
            $field = strtok($field, ';');
            return $field[0] === '"' ? substr($field, 1, -1) : $field;
        }

        $field = $firstName . '=' . $field;
        if (! preg_match_all('%([^=\s]+)\s*=\s*("[^"]+"|[^;]+)(;\s*|$)%', $field, $matches)) {
            throw new Exception\RuntimeException('not a valid header field');
        }

        if ($wantedPart) {
            foreach ($matches[1] as $key => $name) {
                if (strcasecmp($name, $wantedPart)) {
                    continue;
                }
                if ($matches[2][$key][0] !== '"') {
                    return $matches[2][$key];
                }
                return substr($matches[2][$key], 1, -1);
            }
            return;
        }

        $split = [];
        foreach ($matches[1] as $key => $name) {
            $name = strtolower($name);
            if ($matches[2][$key][0] === '"') {
                $split[$name] = substr($matches[2][$key], 1, -1);
            } else {
                $split[$name] = $matches[2][$key];
            }
        }

        return $split;
    }

    
    public static function decodeQuotedPrintable($string)
    {
        return iconv_mime_decode($string, ICONV_MIME_DECODE_CONTINUE_ON_ERROR, 'UTF-8');
    }
}
