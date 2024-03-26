<?php

declare(strict_types=1);

namespace Laminas\Stdlib;

use function function_exists;
use function fwrite;
use function getenv;
use function posix_isatty;
use function preg_replace;
use function sprintf;
use function str_replace;

use const DIRECTORY_SEPARATOR;
use const PHP_EOL;
use const STDERR;
use const STDOUT;


class ConsoleHelper
{
    public const COLOR_GREEN = "\033[32m";
    public const COLOR_RED   = "\033[31m";
    public const COLOR_RESET = "\033[0m";

    public const HIGHLIGHT_INFO  = 'info';
    public const HIGHLIGHT_ERROR = 'error';

    
    private array $highlightMap = [
        self::HIGHLIGHT_INFO  => self::COLOR_GREEN,
        self::HIGHLIGHT_ERROR => self::COLOR_RED,
    ];

    
    private string $eol = PHP_EOL;

    
    private $stderr = STDERR;

    private bool $supportsColor;

    
    public function __construct($resource = STDOUT)
    {
        $this->supportsColor = $this->detectColorCapabilities($resource);
    }

    
    public function colorize($string)
    {
        $reset = $this->supportsColor ? self::COLOR_RESET : '';
        foreach ($this->highlightMap as $key => $color) {
            $pattern = sprintf('#<%s>(.*?)</%s>#s', $key, $key);
            $color   = $this->supportsColor ? $color : '';
            $string  = preg_replace($pattern, $color . '$1' . $reset, $string);
        }
        return $string;
    }

    
    public function write($string, $colorize = true, $resource = STDOUT)
    {
        if ($colorize) {
            $string = $this->colorize($string);
        }

        $string = $this->formatNewlines($string);

        fwrite($resource, $string);
    }

    
    public function writeLine($string, $colorize = true, $resource = STDOUT)
    {
        $this->write($string . $this->eol, $colorize, $resource);
    }

    
    public function writeErrorMessage($message)
    {
        $this->writeLine(sprintf('<error>%s</error>', $message), true, $this->stderr);
        $this->writeLine('', false, $this->stderr);
    }

    
    private function detectColorCapabilities($resource = STDOUT)
    {
        if ('\\' === DIRECTORY_SEPARATOR) {
            
            return false !== getenv('ANSICON')
                || 'ON' === getenv('ConEmuANSI')
                || 'xterm' === getenv('TERM');
        }

        return function_exists('posix_isatty') && posix_isatty($resource);
    }

    
    private function formatNewlines($string)
    {
        $string = str_replace($this->eol, "\0PHP_EOL\0", $string);
        $string = preg_replace("/(\r\n|\n|\r)/", $this->eol, $string);
        return str_replace("\0PHP_EOL\0", $this->eol, $string);
    }
}
