<?php

namespace Spatie\Async\Runtime;

use Closure;
use Laravel\SerializableClosure\SerializableClosure;
use Spatie\Async\Pool;
use Spatie\Async\Process\ParallelProcess;
use Spatie\Async\Process\Runnable;
use Spatie\Async\Process\SynchronousProcess;
use Symfony\Component\Process\Process;

class ParentRuntime
{
    
    protected static $isInitialised = false;

    
    protected static $autoloader;

    
    protected static $childProcessScript;

    protected static $currentId = 0;

    protected static $myPid = null;

    public static function init(string $autoloader = null)
    {
        if (! $autoloader) {
            $existingAutoloaderFiles = array_filter([
                __DIR__.'/../../../../autoload.php',
                __DIR__.'/../../../autoload.php',
                __DIR__.'/../../vendor/autoload.php',
                __DIR__.'/../../../vendor/autoload.php',
            ], function (string $path) {
                return file_exists($path);
            });

            $autoloader = reset($existingAutoloaderFiles);
        }

        self::$autoloader = $autoloader;
        self::$childProcessScript = __DIR__.'/ChildRuntime.php';

        self::$isInitialised = true;
    }

    
    public static function createProcess($task, ?int $outputLength = null, ?string $binary = 'php'): Runnable
    {
        if (! self::$isInitialised) {
            self::init();
        }

        if (! Pool::isSupported()) {
            return SynchronousProcess::create($task, self::getId());
        }

        $process = new Process([
            $binary,
            self::$childProcessScript,
            self::$autoloader,
            self::encodeTask($task),
            $outputLength,
        ]);

        return ParallelProcess::create($process, self::getId());
    }

    
    public static function encodeTask($task): string
    {
        if ($task instanceof Closure) {
            $task = new SerializableClosure($task);
        }

        return base64_encode(serialize($task));
    }

    public static function decodeTask(string $task)
    {
        return unserialize(base64_decode($task));
    }

    protected static function getId(): string
    {
        if (self::$myPid === null) {
            self::$myPid = getmypid();
        }

        self::$currentId += 1;

        return (string) self::$currentId.(string) self::$myPid;
    }
}
