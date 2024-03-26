<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine;

class Logger
{
    
    private $writeDebugLog = false;

    
    private $echoDebugLog = false;

    
    private $debugLog = [];

    
    private $cellStack;

    
    public function __construct(CyclicReferenceStack $stack)
    {
        $this->cellStack = $stack;
    }

    
    public function setWriteDebugLog($pValue): void
    {
        $this->writeDebugLog = $pValue;
    }

    
    public function getWriteDebugLog()
    {
        return $this->writeDebugLog;
    }

    
    public function setEchoDebugLog($pValue): void
    {
        $this->echoDebugLog = $pValue;
    }

    
    public function getEchoDebugLog()
    {
        return $this->echoDebugLog;
    }

    
    public function writeDebugLog(...$args): void
    {
        
        if ($this->writeDebugLog) {
            $message = implode('', $args);
            $cellReference = implode(' -> ', $this->cellStack->showStack());
            if ($this->echoDebugLog) {
                echo $cellReference,
                    ($this->cellStack->count() > 0 ? ' => ' : ''),
                    $message,
                    PHP_EOL;
            }
            $this->debugLog[] = $cellReference .
                ($this->cellStack->count() > 0 ? ' => ' : '') .
                $message;
        }
    }

    
    public function mergeDebugLog(array $args): void
    {
        if ($this->writeDebugLog) {
            foreach ($args as $entry) {
                $this->writeDebugLog($entry);
            }
        }
    }

    
    public function clearLog(): void
    {
        $this->debugLog = [];
    }

    
    public function getLog()
    {
        return $this->debugLog;
    }
}
