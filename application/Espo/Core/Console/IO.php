<?php


namespace Espo\Core\Console;

use RuntimeException;

use const STDOUT;
use const PHP_EOL;


class IO
{
    
    private int $exitStatus = 0;

    
    public function write(string $string): void
    {
        fwrite(STDOUT, $string);
    }

    
    public function writeLine(string $string): void
    {
        fwrite(STDOUT, $string . PHP_EOL);
    }

    
    public function readLine(): string
    {
        return $this->readLineInternal();
    }

    
    public function readSecretLine(): string
    {
        return $this->readLineInternal(true);
    }

    private function readLineInternal(bool $secret = false): string
    {
        $resource = fopen('php:

        if ($resource === false) {
            throw new RuntimeException("Could not open stdin.");
        }

        if ($secret && !self::isWindows()) {
            shell_exec('stty -echo');
        }

        $readString = fgets($resource);

        if ($secret && !self::isWindows()) {
            shell_exec('stty echo');
        }

        if ($readString === false) {
            $readString = '';
        }

        $string = trim($readString);

        fclose($resource);

        return $string;
    }

    private static function isWindows(): bool
    {
        return strcasecmp(substr(PHP_OS, 0, 3), 'WIN') === 0;
    }

    
    public function setExitStatus(int $exitStatus): void
    {
        $this->exitStatus = $exitStatus;
    }

    
    public function getExitStatus(): int
    {
        return $this->exitStatus;
    }
}
