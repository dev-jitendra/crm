<?php

declare(strict_types=1);

namespace OpenSpout\Reader\Common;

use OpenSpout\Reader\Wrapper\XMLReader;
use ReflectionMethod;


final class XMLProcessor
{
    
    public const NODE_TYPE_START = XMLReader::ELEMENT;
    public const NODE_TYPE_END = XMLReader::END_ELEMENT;

    
    public const CALLBACK_REFLECTION_METHOD = 'reflectionMethod';
    public const CALLBACK_REFLECTION_OBJECT = 'reflectionObject';

    
    public const PROCESSING_CONTINUE = 1;
    public const PROCESSING_STOP = 2;

    
    private readonly XMLReader $xmlReader;

    
    private array $callbacks = [];

    
    public function __construct(XMLReader $xmlReader)
    {
        $this->xmlReader = $xmlReader;
    }

    
    public function registerCallback(string $nodeName, int $nodeType, $callback): self
    {
        $callbackKey = $this->getCallbackKey($nodeName, $nodeType);
        $this->callbacks[$callbackKey] = $this->getInvokableCallbackData($callback);

        return $this;
    }

    
    public function readUntilStopped(): void
    {
        while ($this->xmlReader->read()) {
            $nodeType = $this->xmlReader->nodeType;
            $nodeNamePossiblyWithPrefix = $this->xmlReader->name;
            $nodeNameWithoutPrefix = $this->xmlReader->localName;

            $callbackData = $this->getRegisteredCallbackData($nodeNamePossiblyWithPrefix, $nodeNameWithoutPrefix, $nodeType);

            if (null !== $callbackData) {
                $callbackResponse = $this->invokeCallback($callbackData, [$this->xmlReader]);

                if (self::PROCESSING_STOP === $callbackResponse) {
                    
                    break;
                }
            }
        }
    }

    
    private function getCallbackKey(string $nodeName, int $nodeType): string
    {
        return "{$nodeName}{$nodeType}";
    }

    
    private function getInvokableCallbackData($callback): array
    {
        $callbackObject = $callback[0];
        $callbackMethodName = $callback[1];
        $reflectionMethod = new ReflectionMethod($callbackObject, $callbackMethodName);
        $reflectionMethod->setAccessible(true);

        return [
            self::CALLBACK_REFLECTION_METHOD => $reflectionMethod,
            self::CALLBACK_REFLECTION_OBJECT => $callbackObject,
        ];
    }

    
    private function getRegisteredCallbackData(string $nodeNamePossiblyWithPrefix, string $nodeNameWithoutPrefix, int $nodeType): ?array
    {
        
        
        
        $callbackKeyForPossiblyPrefixedName = $this->getCallbackKey($nodeNamePossiblyWithPrefix, $nodeType);
        $callbackKeyForUnPrefixedName = $this->getCallbackKey($nodeNameWithoutPrefix, $nodeType);
        $hasPrefix = ($nodeNamePossiblyWithPrefix !== $nodeNameWithoutPrefix);

        $callbackKeyToUse = $callbackKeyForUnPrefixedName;
        if ($hasPrefix && isset($this->callbacks[$callbackKeyForPossiblyPrefixedName])) {
            $callbackKeyToUse = $callbackKeyForPossiblyPrefixedName;
        }

        
        return $this->callbacks[$callbackKeyToUse] ?? null;
    }

    
    private function invokeCallback(array $callbackData, array $args): int
    {
        $reflectionMethod = $callbackData[self::CALLBACK_REFLECTION_METHOD];
        $callbackObject = $callbackData[self::CALLBACK_REFLECTION_OBJECT];

        return $reflectionMethod->invokeArgs($callbackObject, $args);
    }
}
