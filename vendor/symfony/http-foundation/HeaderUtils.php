<?php



namespace Symfony\Component\HttpFoundation;


class HeaderUtils
{
    public const DISPOSITION_ATTACHMENT = 'attachment';
    public const DISPOSITION_INLINE = 'inline';

    
    private function __construct()
    {
    }

    
    public static function split(string $header, string $separators): array
    {
        $quotedSeparators = preg_quote($separators, '/');

        preg_match_all('
            /
                (?!\s)
                    (?:
                        # quoted-string
                        "(?:[^"\\\\]|\\\\.)*(?:"|\\\\|$)
                    |
                        # token
                        [^"'.$quotedSeparators.']+
                    )+
                (?<!\s)
            |
                # separator
                \s*
                (?<separator>['.$quotedSeparators.'])
                \s*
            /x', trim($header), $matches, \PREG_SET_ORDER);

        return self::groupParts($matches, $separators);
    }

    
    public static function combine(array $parts): array
    {
        $assoc = [];
        foreach ($parts as $part) {
            $name = strtolower($part[0]);
            $value = $part[1] ?? true;
            $assoc[$name] = $value;
        }

        return $assoc;
    }

    
    public static function toString(array $assoc, string $separator): string
    {
        $parts = [];
        foreach ($assoc as $name => $value) {
            if (true === $value) {
                $parts[] = $name;
            } else {
                $parts[] = $name.'='.self::quote($value);
            }
        }

        return implode($separator.' ', $parts);
    }

    
    public static function quote(string $s): string
    {
        if (preg_match('/^[a-z0-9!#$%&\'*.^_`|~-]+$/i', $s)) {
            return $s;
        }

        return '"'.addcslashes($s, '"\\"').'"';
    }

    
    public static function unquote(string $s): string
    {
        return preg_replace('/\\\\(.)|"/', '$1', $s);
    }

    
    public static function makeDisposition(string $disposition, string $filename, string $filenameFallback = ''): string
    {
        if (!\in_array($disposition, [self::DISPOSITION_ATTACHMENT, self::DISPOSITION_INLINE])) {
            throw new \InvalidArgumentException(sprintf('The disposition must be either "%s" or "%s".', self::DISPOSITION_ATTACHMENT, self::DISPOSITION_INLINE));
        }

        if ('' === $filenameFallback) {
            $filenameFallback = $filename;
        }

        
        if (!preg_match('/^[\x20-\x7e]*$/', $filenameFallback)) {
            throw new \InvalidArgumentException('The filename fallback must only contain ASCII characters.');
        }

        
        if (str_contains($filenameFallback, '%')) {
            throw new \InvalidArgumentException('The filename fallback cannot contain the "%" character.');
        }

        
        if (str_contains($filename, '/') || str_contains($filename, '\\') || str_contains($filenameFallback, '/') || str_contains($filenameFallback, '\\')) {
            throw new \InvalidArgumentException('The filename and the fallback cannot contain the "/" and "\\" characters.');
        }

        $params = ['filename' => $filenameFallback];
        if ($filename !== $filenameFallback) {
            $params['filename*'] = "utf-8''".rawurlencode($filename);
        }

        return $disposition.'; '.self::toString($params, ';');
    }

    
    public static function parseQuery(string $query, bool $ignoreBrackets = false, string $separator = '&'): array
    {
        $q = [];

        foreach (explode($separator, $query) as $v) {
            if (false !== $i = strpos($v, "\0")) {
                $v = substr($v, 0, $i);
            }

            if (false === $i = strpos($v, '=')) {
                $k = urldecode($v);
                $v = '';
            } else {
                $k = urldecode(substr($v, 0, $i));
                $v = substr($v, $i);
            }

            if (false !== $i = strpos($k, "\0")) {
                $k = substr($k, 0, $i);
            }

            $k = ltrim($k, ' ');

            if ($ignoreBrackets) {
                $q[$k][] = urldecode(substr($v, 1));

                continue;
            }

            if (false === $i = strpos($k, '[')) {
                $q[] = bin2hex($k).$v;
            } else {
                $q[] = bin2hex(substr($k, 0, $i)).rawurlencode(substr($k, $i)).$v;
            }
        }

        if ($ignoreBrackets) {
            return $q;
        }

        parse_str(implode('&', $q), $q);

        $query = [];

        foreach ($q as $k => $v) {
            if (false !== $i = strpos($k, '_')) {
                $query[substr_replace($k, hex2bin(substr($k, 0, $i)).'[', 0, 1 + $i)] = $v;
            } else {
                $query[hex2bin($k)] = $v;
            }
        }

        return $query;
    }

    private static function groupParts(array $matches, string $separators, bool $first = true): array
    {
        $separator = $separators[0];
        $partSeparators = substr($separators, 1);

        $i = 0;
        $partMatches = [];
        $previousMatchWasSeparator = false;
        foreach ($matches as $match) {
            if (!$first && $previousMatchWasSeparator && isset($match['separator']) && $match['separator'] === $separator) {
                $previousMatchWasSeparator = true;
                $partMatches[$i][] = $match;
            } elseif (isset($match['separator']) && $match['separator'] === $separator) {
                $previousMatchWasSeparator = true;
                ++$i;
            } else {
                $previousMatchWasSeparator = false;
                $partMatches[$i][] = $match;
            }
        }

        $parts = [];
        if ($partSeparators) {
            foreach ($partMatches as $matches) {
                $parts[] = self::groupParts($matches, $partSeparators, false);
            }
        } else {
            foreach ($partMatches as $matches) {
                $parts[] = self::unquote($matches[0][0]);
            }

            if (!$first && 2 < \count($parts)) {
                $parts = [
                    $parts[0],
                    implode($separator, \array_slice($parts, 1)),
                ];
            }
        }

        return $parts;
    }
}
