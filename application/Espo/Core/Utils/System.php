<?php


namespace Espo\Core\Utils;

use Symfony\Component\Process\PhpExecutableFinder;

class System
{
    
    public function getServerType(): string
    {
        $serverSoft = $_SERVER['SERVER_SOFTWARE'];

        preg_match('/^(.*?)\

        if (empty($match[1])) {
            preg_match('/^(.*)\/?/i', $serverSoft, $match);
        }

        return strtolower(
            trim($match[1])
        );
    }

    
    public function getOS(): ?string
    {
        $osList = [
            'windows' => [
                'win',
                'UWIN',
            ],
            'mac' => [
                'mac',
                'darwin',
            ],
            'linux' => [
                'linux',
                'cygwin',
                'GNU',
                'FreeBSD',
                'OpenBSD',
                'NetBSD',
            ],
        ];

        $sysOS = strtolower(PHP_OS);

        foreach ($osList as $osName => $osSystem) {
            if (preg_match('/^('.implode('|', $osSystem).')/i', $sysOS)) {
                return $osName;
            }
        }

        return null;
    }

    
    public function getRootDir(): string
    {
        $bPath = realpath('bootstrap.php') ?: '';

        return dirname($bPath);
    }

    
    public function getPhpBinary(): ?string
    {
        $path = (new PhpExecutableFinder)->find();

        if ($path === false) {
            return null;
        }

        return $path;
    }

    
    public static function getPhpVersion(): string
    {
        $version = phpversion();

        $matches = null;

        if (preg_match('/^[0-9\.]+[0-9]/', $version, $matches)) {
            return $matches[0];
        }

        return $version;
    }

    
    public function getPhpParam(string $name)
    {
        return ini_get($name);
    }

    
    public function hasPhpExtension(string $name): bool
    {
        return extension_loaded($name);
    }

    
    public function hasPhpLib(string $name): bool
    {
        return extension_loaded($name);
    }

    
    public static function getPid(): ?int
    {
        if (!function_exists('getmypid')) {
            return null;
        }

        $pid = getmypid();

        if ($pid === false) {
            return null;
        }

        return $pid;
    }

    public static function isProcessActive(?int $pid): bool
    {
        if ($pid === null) {
            return false;
        }

        if (!self::isPosixSupported()) {
            return false;
        }

        if (posix_getsid($pid) === false) {
            return false;
        }

        return true;
    }

    public static function isPosixSupported(): bool
    {
        return function_exists('posix_getsid');
    }
}
