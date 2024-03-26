<?php

declare(strict_types=1);

namespace Laminas\Stdlib\StringWrapper;

use Laminas\Stdlib\Exception;

use function assert;
use function extension_loaded;
use function iconv;
use function iconv_strlen;
use function iconv_strpos;
use function iconv_substr;

class Iconv extends AbstractStringWrapper
{
    
    protected static $encodings = [
        
        'ASCII',
        'ISO-8859-1',
        'ISO-8859-2',
        'ISO-8859-3',
        'ISO-8859-4',
        'ISO-8859-5',
        'ISO-8859-7',
        'ISO-8859-9',
        'ISO-8859-10',
        'ISO-8859-13',
        'ISO-8859-14',
        'ISO-8859-15',
        'ISO-8859-16',
        'KOI8-R',
        'KOI8-U',
        'KOI8-RU',
        'CP1250',
        'CP1251',
        'CP1252',
        'CP1253',
        'CP1254',
        'CP1257',
        'CP850',
        'CP866',
        'CP1131',
        'MACROMAN',
        'MACCENTRALEUROPE',
        'MACICELAND',
        'MACCROATIAN',
        'MACROMANIA',
        'MACCYRILLIC',
        'MACUKRAINE',
        'MACGREEK',
        'MACTURKISH',
        'MACINTOSH',

        
        'ISO-8859-6',
        'ISO-8859-8',
        'CP1255',
        'CP1256',
        'CP862',
        'MACHEBREW',
        'MACARABIC',

        
        'EUC-JP',
        'SHIFT_JIS',
        'CP932',
        'ISO-2022-JP',
        'ISO-2022-JP-2',
        'ISO-2022-JP-1',

        
        'EUC-CN',
        'HZ',
        'GBK',
        'CP936',
        'GB18030',
        'EUC-TW',
        'BIG5',
        'CP950',
        'BIG5-HKSCS',
        'BIG5-HKSCS:2004',
        'BIG5-HKSCS:2001',
        'BIG5-HKSCS:1999',
        'ISO-2022-CN',
        'ISO-2022-CN-EXT',

        
        'EUC-KR',
        'CP949',
        'ISO-2022-KR',
        'JOHAB',

        
        'ARMSCII-8',

        
        'GEORGIAN-ACADEMY',
        'GEORGIAN-PS',

        
        'KOI8-T',

        
        'PT154',
        'RK1048',

        
        'ISO-8859-11',
        'TIS-620',
        'CP874',
        'MACTHAI',

        
        'MULELAO-1',
        'CP1133',

        
        'VISCII',
        'TCVN',
        'CP1258',

        
        'HP-ROMAN8',
        'NEXTSTEP',

        
        'UTF-8',
        'UCS-2',
        'UCS-2BE',
        'UCS-2LE',
        'UCS-4',
        'UCS-4BE',
        'UCS-4LE',
        'UTF-16',
        'UTF-16BE',
        'UTF-16LE',
        'UTF-32',
        'UTF-32BE',
        'UTF-32LE',
        'UTF-7',
        'C99',
        'JAVA',

        

        
        

        
        'CP437',
        'CP737',
        'CP775',
        'CP852',
        'CP853',
        'CP855',
        'CP857',
        'CP858',
        'CP860',
        'CP861',
        'CP863',
        'CP865',
        'CP869',
        'CP1125',

        
        'CP864',

        
        'EUC-JISX0213',
        'Shift_JISX0213',
        'ISO-2022-JP-3',

        
        'BIG5-2003', 

        
        'TDS565',

        
        'ATARIST',
        'RISCOS-LATIN1',
    ];

    
    public static function getSupportedEncodings()
    {
        return static::$encodings;
    }

    
    public function __construct()
    {
        if (! extension_loaded('iconv')) {
            throw new Exception\ExtensionNotLoadedException(
                'PHP extension "iconv" is required for this wrapper'
            );
        }
    }

    
    public function strlen($str)
    {
        return iconv_strlen($str, $this->getEncoding());
    }

    
    public function substr($str, $offset = 0, $length = null)
    {
        $length ??= $this->strlen($str);
        assert($length !== false);

        return iconv_substr($str, $offset, $length, $this->getEncoding());
    }

    
    public function strpos($haystack, $needle, $offset = 0)
    {
        $encoding = $this->getEncoding();
        assert($encoding !== null);

        return iconv_strpos($haystack, $needle, $offset, $encoding);
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

        $fromEncoding = $reverse ? $convertEncoding : $encoding;
        $toEncoding   = $reverse ? $encoding : $convertEncoding;

        if (null === $toEncoding || null === $fromEncoding) {
            return $str;
        }

        
        
        return iconv($fromEncoding, $toEncoding . '
    }
}
